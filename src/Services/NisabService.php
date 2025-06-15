<?php
namespace Tawfik\ZakatCalculator\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class NisabService
{
    protected $config;
    protected $cache;
    protected $goldPrice;
    protected $silverPrice;

    public function __construct(ConfigRepository $config, CacheRepository $cache)
    {
        $this->config = $config;
        $this->cache  = $cache;
        $this->loadDefaultPrices();
    }

    /**
     * Set current gold price per gram
     */
    public function setGoldPrice(float $price): self
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Gold price cannot be negative');
        }

        $this->goldPrice = $price;
        $this->cache->put('zakat.gold_price', $price, $this->config->get('zakat.cache.ttl', 3600));

        return $this;
    }

    /**
     * Set current silver price per gram
     */
    public function setSilverPrice(float $price): self
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('Silver price cannot be negative');
        }

        $this->silverPrice = $price;
        $this->cache->put('zakat.silver_price', $price, $this->config->get('zakat.cache.ttl', 3600));

        return $this;
    }

    /**
     * Get current gold price per gram
     */
    public function getGoldPrice(): float
    {
        return $this->goldPrice;
    }

    /**
     * Get current silver price per gram
     */
    public function getSilverPrice(): float
    {
        return $this->silverPrice;
    }

    /**
     * Calculate gold nisab value
     * @link https://islamqa.info/ar/answers/144734
     */
    public function calculateGoldNisab(): float
    {
        $goldNisabWeight = $this->config->get('zakat.nisab.gold', 85);
        return $goldNisabWeight * $this->goldPrice;
    }

    /**
     * Calculate silver nisab value
     * @link https://islamqa.info/ar/answers/144734
     */
    public function calculateSilverNisab(): float
    {
        $silverNisabWeight = $this->config->get('zakat.nisab.silver', 595);
        return $silverNisabWeight * $this->silverPrice;
    }

    /**
     * Calculate cash/business nisab value (based on silver)
     * @link https://islamqa.info/ar/answers/144734
     */
    public function calculateCashNisab(): float
    {
        return $this->calculateSilverNisab();
    }

    /**
     * Check if agricultural product meets nisab threshold
     * @link https://islamqa.info/ar/answers/144734
     */
    public function checkAgriculturalNisab(string $cropType, float $weight): bool
    {
        $wasqWeight = $this->config->get("zakat.agricultural.wasq_weights.{$cropType}",
            $this->config->get('zakat.agricultural.wasq_weights.default', 60));

        // 5 wasqs is the nisab threshold for agricultural products
        $nisabWeight = $wasqWeight * 5;

        return $weight >= $nisabWeight;
    }

    /**
     * Get agricultural zakat rate
     */
    public function getAgriculturalRate(bool $irrigated): float
    {
        return $irrigated
        ? $this->config->get('zakat.agricultural.irrigated_rate', 0.05)
        : $this->config->get('zakat.agricultural.non_irrigated_rate', 0.1);
    }

    /**
     * Load default prices from cache or config
     */
    public function loadDefaultPrices(): void
    {
        if ($this->config->get('zakat.cache.enabled', true)) {
            $this->goldPrice = $this->cache->remember(
                'zakat.gold_price',
                $this->config->get('zakat.cache.ttl', 3600),
                function () {
                    return $this->config->get('zakat.default_prices.gold', 100);
                }
            );

            $this->silverPrice = $this->cache->remember(
                'zakat.silver_price',
                $this->config->get('zakat.cache.ttl', 3600),
                function () {
                    return $this->config->get('zakat.default_prices.silver', 10);
                }
            );
        } else {
            $this->goldPrice   = $this->config->get('zakat.default_prices.gold', 100);
            $this->silverPrice = $this->config->get('zakat.default_prices.silver', 10);
        }
    }

    public function getGoldNisabValue(): float
    {
        return $this->config->get('zakat.nisab.gold', 85) * $this->goldPrice;
    }

    public function getSilverNisabValue(): float
    {
        return $this->config->get('zakat.nisab.silver', 595) * $this->silverPrice;
    }

    public function getAgriculturalNisabWeight(string $product): float
    {
        return $this->config->get("zakat.agricultural.wasq_weights.{$product}", 60);
    }

    public function getAgriculturalZakatRate(bool $isIrrigated): float
    {
        return $isIrrigated
        ? $this->config->get('zakat.agricultural.irrigated_rate', 0.05)
        : $this->config->get('zakat.agricultural.non_irrigated_rate', 0.1);
    }
}
