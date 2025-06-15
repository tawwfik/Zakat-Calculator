<?php
namespace Tawfik\ZakatCalculator;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Tawfik\ZakatCalculator\Exceptions\InvalidInputException;
use Tawfik\ZakatCalculator\Services\NisabService;

class ZakatCalculator
{
    protected $cache;
    protected $config;
    protected $nisabService;
    protected $assets = [
        'cash'         => 0,
        'gold'         => [],
        'silver'       => 0,
        'business'     => [],
        'agricultural' => [],
    ];

    public function __construct(ConfigRepository $config, CacheRepository $cache)
    {
        $this->config       = $config;
        $this->cache        = $cache;
        $this->nisabService = new NisabService($config, $cache);
    }

    public function setCashAmount(float $amount): self
    {
        if ($amount < 0) {
            throw new InvalidInputException('Cash amount cannot be negative');
        }
        $this->assets['cash'] = $amount;
        return $this;
    }

    public function setGoldPrice(float $price): self
    {
        if ($price < 0) {
            throw new InvalidInputException('Gold price cannot be negative');
        }
        $this->nisabService->setGoldPrice($price);
        return $this;
    }

    public function setSilverPrice(float $price): self
    {
        if ($price < 0) {
            throw new InvalidInputException('Silver price cannot be negative');
        }
        $this->nisabService->setSilverPrice($price);
        return $this;
    }

    public function addGoldItem(int $karat, float $weight): self
    {
        if (! in_array($karat, $this->config->get('zakat.supported_karats'))) {
            throw new InvalidInputException("Unsupported gold karat: {$karat}");
        }

        if ($weight < 0) {
            throw new InvalidInputException('Gold weight cannot be negative');
        }

        $this->assets['gold'][] = [
            'karat'  => $karat,
            'weight' => $weight,
        ];

        return $this;
    }

    public function setSilverWeight(float $weight): self
    {
        if ($weight < 0) {
            throw new InvalidInputException('Silver weight cannot be negative');
        }
        $this->assets['silver'] = $weight;
        return $this;
    }

    public function setBusinessAssets(array $assets): self
    {
        foreach ($assets as $key => $value) {
            if ($value < 0) {
                throw new InvalidInputException("Business asset '{$key}' cannot be negative");
            }
        }
        $this->assets['business'] = $assets;
        return $this;
    }

    public function setAgriculturalProducts(array $products): self
    {
        foreach ($products as $type => $product) {
            if ($product['weight'] < 0) {
                throw new InvalidInputException("Agricultural product '{$type}' weight cannot be negative");
            }
        }
        $this->assets['agricultural'] = $products;
        return $this;
    }

    public function calculate(): array
    {
        $result = [
            'total_zakat'        => 0,
            'calculation_method' => $this->config->get('zakat.calculation_methods.default'),
            'details'            => [],
        ];

        // Calculate cash zakat
        if ($this->assets['cash'] > 0) {
            $cashZakat = $this->calculateCashZakat();
            $result['total_zakat'] += $cashZakat;
            $result['details']['cash'] = [
                'amount' => $this->assets['cash'],
                'zakat'  => $cashZakat,
            ];
        }

        // Calculate gold zakat
        if (! empty($this->assets['gold'])) {
            $goldZakat = $this->calculateGoldZakat();
            $result['total_zakat'] += $goldZakat;
            $result['details']['gold'] = [
                'items' => $this->assets['gold'],
                'zakat' => $goldZakat,
            ];
        }

        // Calculate silver zakat
        if ($this->assets['silver'] > 0) {
            $silverZakat = $this->calculateSilverZakat();
            $result['total_zakat'] += $silverZakat;
            $result['details']['silver'] = [
                'weight' => $this->assets['silver'],
                'zakat'  => $silverZakat,
            ];
        }

        // Calculate business zakat
        if (! empty($this->assets['business'])) {
            $businessZakat = $this->calculateBusinessZakat();
            $result['total_zakat'] += $businessZakat;
            $result['details']['business'] = [
                'assets' => $this->assets['business'],
                'zakat'  => $businessZakat,
            ];
        }

        // Calculate agricultural zakat
        if (! empty($this->assets['agricultural'])) {
            $agriculturalZakat = $this->calculateAgriculturalZakat();
            $result['total_zakat'] += $agriculturalZakat;
            $result['details']['agricultural'] = [
                'products' => $this->assets['agricultural'],
                'zakat'    => $agriculturalZakat,
            ];
        }

        return $result;
    }

    protected function calculateCashZakat(): float
    {
        $nisabValue = $this->nisabService->getGoldNisabValue();
        if ($this->assets['cash'] < $nisabValue) {
            return 0;
        }

        return $this->assets['cash'] * 0.025;
    }

    protected function calculateGoldZakat(): float
    {
        $totalValue = 0;
        foreach ($this->assets['gold'] as $item) {
            $totalValue += $item['weight'] * $this->nisabService->getGoldPrice();
        }

        $nisabValue = $this->nisabService->getGoldNisabValue();
        if ($totalValue < $nisabValue) {
            return 0;
        }

        return $totalValue * 0.025;
    }

    protected function calculateSilverZakat(): float
    {
        $totalValue = $this->assets['silver'] * $this->nisabService->getSilverPrice();
        $nisabValue = $this->nisabService->getSilverNisabValue();

        if ($totalValue < $nisabValue) {
            return 0;
        }

        return $totalValue * 0.025;
    }

    protected function calculateBusinessZakat(): float
    {
        $totalValue = array_sum($this->assets['business']);
        $nisabValue = $this->nisabService->getGoldNisabValue();

        if ($totalValue < $nisabValue) {
            return 0;
        }

        return $totalValue * 0.025;
    }

    protected function calculateAgriculturalZakat(): float
    {
        $totalZakat = 0;

        foreach ($this->assets['agricultural'] as $type => $product) {
            $nisabWeight = $this->nisabService->getAgriculturalNisabWeight($type);
            if ($product['weight'] < $nisabWeight) {
                continue;
            }

            $rate = $this->nisabService->getAgriculturalZakatRate($product['is_irrigated']);
            $totalZakat += $product['weight'] * $rate;
        }

        return $totalZakat;
    }
}
