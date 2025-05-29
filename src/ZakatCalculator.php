<?php
namespace Tawfik\ZakatCalculator;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Tawfik\ZakatCalculator\Exceptions\InvalidInputException;

class ZakatCalculator
{
    protected float $cash         = 0;
    protected array $goldItems    = [];
    protected float $silverWeight = 0;
    protected float $goldPrice    = 0;
    protected float $silverPrice  = 0;
    protected string $calculationMethod;
    protected array $businessAssets       = [];
    protected array $agriculturalProducts = [];
    protected CacheRepository $cache;
    protected ConfigRepository $config;

    public function __construct(CacheRepository $cache, ConfigRepository $config)
    {
        $this->cache             = $cache;
        $this->config            = $config;
        $this->calculationMethod = $this->config->get('zakat.calculation_methods.default', 'hanafi');
        $this->loadDefaultPrices();
    }

    /**
     * Set the cash amount for zakat calculation
     *
     * @param float $cash
     * @return self
     * @throws InvalidInputException
     */
    public function setCash(float $cash): self
    {
        if ($cash < 0) {
            throw InvalidInputException::negativeValue('cash');
        }
        $this->cash = $cash;
        return $this;
    }

    /**
     * Set gold items for zakat calculation
     *
     * @param array $goldItems Array of gold items with weight and karat
     * @return self
     * @throws InvalidInputException
     */
    public function setGoldItems(array $goldItems): self
    {
        foreach ($goldItems as $item) {
            if (! isset($item['weight']) || $item['weight'] < 0) {
                throw InvalidInputException::negativeValue('gold weight');
            }
            if (! isset($item['karat']) || ! in_array($item['karat'], $this->config->get('zakat.supported_karats'))) {
                throw InvalidInputException::invalidKarat($item['karat'] ?? 0);
            }
        }
        $this->goldItems = $goldItems;
        return $this;
    }

    /**
     * Set silver weight for zakat calculation
     *
     * @param float $silverWeight
     * @return self
     * @throws InvalidInputException
     */
    public function setSilverWeight(float $silverWeight): self
    {
        if ($silverWeight < 0) {
            throw InvalidInputException::negativeValue('silver weight');
        }
        $this->silverWeight = $silverWeight;
        return $this;
    }

    /**
     * Set gold price per gram
     *
     * @param float $goldPrice
     * @return self
     * @throws InvalidInputException
     */
    public function setGoldPrice(float $goldPrice): self
    {
        if ($goldPrice < 0) {
            throw InvalidInputException::negativeValue('gold price');
        }
        $this->goldPrice = $goldPrice;
        return $this;
    }

    /**
     * Set silver price per gram
     *
     * @param float $silverPrice
     * @return self
     * @throws InvalidInputException
     */
    public function setSilverPrice(float $silverPrice): self
    {
        if ($silverPrice < 0) {
            throw InvalidInputException::negativeValue('silver price');
        }
        $this->silverPrice = $silverPrice;
        return $this;
    }

    /**
     * Set business assets for zakat calculation
     *
     * @param array $assets
     * @return self
     */
    public function setBusinessAssets(array $assets): self
    {
        $this->businessAssets = $assets;
        return $this;
    }

    /**
     * Set agricultural products for zakat calculation
     *
     * @param array $products
     * @return self
     */
    public function setAgriculturalProducts(array $products): self
    {
        $this->agriculturalProducts = $products;
        return $this;
    }

    /**
     * Set calculation method
     *
     * @param string $method
     * @return self
     * @throws InvalidInputException
     */
    public function setCalculationMethod(string $method): self
    {
        if (! in_array($method, ['hanafi', 'shafi', 'maliki', 'hanbali'])) {
            throw InvalidInputException::invalidCalculationMethod($method);
        }
        $this->calculationMethod = $method;
        return $this;
    }

    /**
     * Calculate zakat based on all set values
     *
     * @return array
     */
    public function calculate(): array
    {
        $goldValue         = $this->calculateGoldValue();
        $silverValue       = $this->calculateSilverValue();
        $businessValue     = $this->calculateBusinessValue();
        $agriculturalValue = $this->calculateAgriculturalValue();

        $total       = $this->cash + $goldValue + $silverValue + $businessValue + $agriculturalValue;
        $nisab       = $this->calculateNisab();
        $zakatDue    = $total >= $nisab;
        $zakatAmount = $zakatDue ? $total * 0.025 : 0;

        return [
            'cash'               => $this->formatAmount($this->cash),
            'gold_value'         => $this->formatAmount($goldValue),
            'silver_value'       => $this->formatAmount($silverValue),
            'business_value'     => $this->formatAmount($businessValue),
            'agricultural_value' => $this->formatAmount($agriculturalValue),
            'total'              => $this->formatAmount($total),
            'nisab'              => $this->formatAmount($nisab),
            'zakat_due'          => $zakatDue,
            'zakat_amount'       => $this->formatAmount($zakatAmount),
            'calculation_method' => $this->calculationMethod,
        ];
    }

    /**
     * Calculate the value of gold items
     *
     * @return float
     */
    protected function calculateGoldValue(): float
    {
        $goldValue = 0;
        foreach ($this->goldItems as $item) {
            $weight     = $item['weight'];
            $karat      = $item['karat'];
            $pureWeight = $weight * ($karat / 24);
            $goldValue += $pureWeight * $this->goldPrice;
        }
        return $goldValue;
    }

    /**
     * Calculate the value of silver
     *
     * @return float
     */
    protected function calculateSilverValue(): float
    {
        return $this->silverWeight * $this->silverPrice;
    }

    /**
     * Calculate the value of business assets
     *
     * @return float
     */
    protected function calculateBusinessValue(): float
    {
        return array_sum($this->businessAssets);
    }

    /**
     * Calculate the value of agricultural products
     *
     * @return float
     */
    protected function calculateAgriculturalValue(): float
    {
        $value = 0;
        foreach ($this->agriculturalProducts as $product) {
            $rate = $product['irrigated'] ?
            $this->config->get('zakat.agricultural.irrigated_rate') :
            $this->config->get('zakat.agricultural.non_irrigated_rate');
            $value += $product['value'] * $rate;
        }
        return $value;
    }

    /**
     * Calculate the nisab threshold
     *
     * @return float
     */
    protected function calculateNisab(): float
    {
        return $this->config->get('zakat.nisab.gold') * $this->goldPrice;
    }

    /**
     * Format amount with proper precision
     *
     * @param float $amount
     * @return float
     */
    protected function formatAmount(float $amount): float
    {
        return round($amount, $this->config->get('zakat.precision', 2));
    }

    /**
     * Load default prices from cache or config
     */
    protected function loadDefaultPrices(): void
    {
        if ($this->config->get('zakat.cache.enabled')) {
            $this->goldPrice = $this->cache->remember('gold_price', $this->config->get('zakat.cache.ttl'), function () {
                return $this->config->get('zakat.default_prices.gold');
            });
            $this->silverPrice = $this->cache->remember('silver_price', $this->config->get('zakat.cache.ttl'), function () {
                return $this->config->get('zakat.default_prices.silver');
            });
        } else {
            $this->goldPrice   = $this->config->get('zakat.default_prices.gold');
            $this->silverPrice = $this->config->get('zakat.default_prices.silver');
        }
    }
}
