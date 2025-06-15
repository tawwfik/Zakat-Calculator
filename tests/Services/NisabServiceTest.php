<?php
namespace Tawfik\ZakatCalculator\Tests\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tawfik\ZakatCalculator\Services\NisabService;

class NisabServiceTest extends TestCase
{
    protected $config;
    protected $cache;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = Mockery::mock(ConfigRepository::class);
        $this->cache  = Mockery::mock(CacheRepository::class);

        // Set default config values
        $this->config->shouldReceive('get')->with('zakat.nisab.gold', 85)->andReturn(85);
        $this->config->shouldReceive('get')->with('zakat.nisab.silver', 595)->andReturn(595);
        $this->config->shouldReceive('get')->with('zakat.cache.enabled', true)->andReturn(true);
        $this->config->shouldReceive('get')->with('zakat.cache.ttl', 3600)->andReturn(3600);
        $this->config->shouldReceive('get')->with('zakat.default_prices.gold', 100)->andReturn(100);
        $this->config->shouldReceive('get')->with('zakat.default_prices.silver', 10)->andReturn(10);
        $this->config->shouldReceive('get')->with('zakat.agricultural.wasq_weights.wheat', 60)->andReturn(60);
        $this->config->shouldReceive('get')->with('zakat.agricultural.irrigated_rate', 0.05)->andReturn(0.05);
        $this->config->shouldReceive('get')->with('zakat.agricultural.non_irrigated_rate', 0.1)->andReturn(0.1);
        $this->config->shouldReceive('get')->with('zakat.calculation_methods.default', 'hanafi')->andReturn('hanafi');
        $this->config->shouldReceive('get')->with('zakat.supported_karats', [24, 22, 21, 18, 14, 12, 10])->andReturn([24, 22, 21, 18, 14, 12, 10]);

        // Set up cache expectations
        $this->cache->shouldReceive('remember')
            ->with('zakat.gold_price', 3600, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->cache->shouldReceive('remember')
            ->with('zakat.silver_price', 3600, Mockery::type('Closure'))
            ->andReturnUsing(function ($key, $ttl, $callback) {
                return $callback();
            });

        $this->cache->shouldReceive('put')
            ->with('zakat.gold_price', Mockery::any(), 3600)
            ->andReturn(true);

        $this->cache->shouldReceive('put')
            ->with('zakat.silver_price', Mockery::any(), 3600)
            ->andReturn(true);

        $this->service = new NisabService($this->config, $this->cache);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCanSetAndGetGoldPrice()
    {
        $this->service->setGoldPrice(100);
        $this->assertEquals(100, $this->service->getGoldPrice());
    }

    public function testCanSetAndGetSilverPrice()
    {
        $this->service->setSilverPrice(10);
        $this->assertEquals(10, $this->service->getSilverPrice());
    }

    public function testCanCalculateGoldNisab()
    {
        $this->service->setGoldPrice(100);
        $this->assertEquals(8500, $this->service->getGoldNisabValue());
    }

    public function testCanCalculateSilverNisab()
    {
        $this->service->setSilverPrice(10);
        $this->assertEquals(5950, $this->service->getSilverNisabValue());
    }

    public function testGetAgriculturalNisabWeight()
    {
        $this->assertEquals(60, $this->service->getAgriculturalNisabWeight('wheat'));
    }

    public function testGetAgriculturalZakatRate()
    {
        $this->assertEquals(0.05, $this->service->getAgriculturalZakatRate(true));
        $this->assertEquals(0.1, $this->service->getAgriculturalZakatRate(false));
    }

    public function testLoadDefaultPricesFromCache()
    {
        $this->cache->shouldReceive('remember')
            ->with('zakat.gold_price', 3600, Mockery::type('Closure'))
            ->andReturn(100);

        $this->cache->shouldReceive('remember')
            ->with('zakat.silver_price', 3600, Mockery::type('Closure'))
            ->andReturn(10);

        $this->service->loadDefaultPrices();
        $this->assertEquals(100, $this->service->getGoldPrice());
        $this->assertEquals(10, $this->service->getSilverPrice());
    }

    public function testLoadDefaultPricesFromConfig()
    {
        $this->cache->shouldReceive('remember')
            ->with('zakat.gold_price', 3600, Mockery::type('Closure'))
            ->andReturn(null);

        $this->cache->shouldReceive('remember')
            ->with('zakat.silver_price', 3600, Mockery::type('Closure'))
            ->andReturn(null);

        $this->service->loadDefaultPrices();
        $this->assertEquals(100, $this->service->getGoldPrice());
        $this->assertEquals(10, $this->service->getSilverPrice());
    }
}
