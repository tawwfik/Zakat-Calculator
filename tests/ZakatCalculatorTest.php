<?php
namespace Tawfik\ZakatCalculator\Tests;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery;
use PHPUnit\Framework\TestCase;
use Tawfik\ZakatCalculator\Exceptions\InvalidInputException;
use Tawfik\ZakatCalculator\ZakatCalculator;

class ZakatCalculatorTest extends TestCase
{
    protected $config;
    protected $cache;
    protected $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = Mockery::mock(ConfigRepository::class);
        $this->cache  = Mockery::mock(CacheRepository::class);

        // Set up config mock to handle any get() call with default values
        $this->config->shouldReceive('get')->andReturnUsing(function ($key, $default = null) {
            $defaults = [
                'zakat.nisab.gold'                      => 85,
                'zakat.nisab.silver'                    => 595,
                'zakat.cache.enabled'                   => true,
                'zakat.cache.ttl'                       => 3600,
                'zakat.default_prices.gold'             => 100,
                'zakat.default_prices.silver'           => 10,
                'zakat.agricultural.wasq_weights.wheat' => 60,
                'zakat.agricultural.irrigated_rate'     => 0.05,
                'zakat.agricultural.non_irrigated_rate' => 0.1,
                'zakat.calculation_methods.default'     => 'hanafi',
                'zakat.supported_karats'                => [24, 22, 21, 18, 14, 12, 10],
                'zakat.business.include_inventory'      => true,
                'zakat.business.include_receivables'    => true,
                'zakat.business.include_cash_at_bank'   => true,
                'zakat.business.include_cash_in_hand'   => true,
            ];

            return $defaults[$key] ?? $default;
        });

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

        $this->calculator = new ZakatCalculator($this->config, $this->cache);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCanCalculateCashZakat()
    {
        $this->calculator->setCashAmount(10000);
        $result = $this->calculator->calculate();
        $this->assertEquals(250, $result['details']['cash']['zakat']);
    }

    public function testCanCalculateGoldZakat()
    {
        $this->calculator->addGoldItem(24, 100);
        $result = $this->calculator->calculate();
        $this->assertEquals(250, $result['details']['gold']['zakat']);
    }

    public function testCanCalculateSilverZakat()
    {
        $this->calculator->setSilverWeight(600);
        $result = $this->calculator->calculate();
        $this->assertEquals(150, $result['details']['silver']['zakat']);
    }

    public function testCanCalculateBusinessZakat()
    {
        $this->calculator->setBusinessAssets([
            'inventory'    => 10000,
            'receivables'  => 0,
            'cash_at_bank' => 0,
        ]);
        $result = $this->calculator->calculate();
        $this->assertEquals(250, $result['details']['business']['zakat']);
    }

    public function testCanCalculateAgriculturalZakat()
    {
        $this->calculator->setAgriculturalProducts([
            'wheat' => [
                'weight'       => 400,
                'is_irrigated' => true,
            ],
        ]);
        $result = $this->calculator->calculate();
        $this->assertEquals(20, $result['details']['agricultural']['zakat']);
    }

    public function testThrowsExceptionForNegativeCashAmount()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setCashAmount(-1000);
    }

    public function testThrowsExceptionForNegativeGoldPrice()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setGoldPrice(-100);
    }

    public function testThrowsExceptionForNegativeSilverPrice()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setSilverPrice(-10);
    }

    public function testThrowsExceptionForInvalidGoldKarat()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->addGoldItem(15, 100);
    }

    public function testThrowsExceptionForNegativeGoldWeight()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->addGoldItem(24, -100);
    }

    public function testThrowsExceptionForNegativeSilverWeight()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setSilverWeight(-500);
    }

    public function testThrowsExceptionForNegativeBusinessAssets()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setBusinessAssets([
            'inventory'    => -1000,
            'receivables'  => 0,
            'cash_at_bank' => 0,
        ]);
    }

    public function testThrowsExceptionForNegativeAgriculturalWeight()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setAgriculturalProducts([
            'wheat' => [
                'weight'       => -400,
                'is_irrigated' => true,
            ],
        ]);
    }

    public function testItCalculatesGoldZakatCorrectly()
    {
        $this->calculator->addGoldItem(24, 100);
        $result = $this->calculator->calculate();
        $this->assertEquals(250, $result['details']['gold']['zakat']);
    }

    public function testItCalculatesSilverZakatCorrectly()
    {
        $this->calculator->setSilverWeight(600);
        $result = $this->calculator->calculate();
        $this->assertEquals(150, $result['details']['silver']['zakat']);
    }

    public function testItCalculatesBusinessZakatCorrectly()
    {
        $this->calculator->setBusinessAssets([
            'inventory'    => 10000,
            'receivables'  => 0,
            'cash_at_bank' => 0,
        ]);
        $result = $this->calculator->calculate();
        $this->assertEquals(250, $result['details']['business']['zakat']);
    }

    public function testItCalculatesAgriculturalZakatCorrectly()
    {
        $this->calculator->setAgriculturalProducts([
            'wheat' => [
                'weight'       => 400,
                'is_irrigated' => true,
            ],
        ]);
        $result = $this->calculator->calculate();
        $this->assertEquals(20, $result['details']['agricultural']['zakat']);
    }

    public function testItCalculatesTotalZakatCorrectly()
    {
        $this->calculator
            ->setCashAmount(10000)
            ->addGoldItem(24, 100)
            ->setSilverWeight(600)
            ->setBusinessAssets([
                'inventory'    => 10000,
                'receivables'  => 0,
                'cash_at_bank' => 0,
            ])
            ->setAgriculturalProducts([
                'wheat' => [
                    'weight'       => 400,
                    'is_irrigated' => true,
                ],
            ]);

        $result = $this->calculator->calculate();
        $this->assertEquals(920, $result['total_zakat']);
    }

    public function testItReturnsDetailedGoldCalculation()
    {
        $this->calculator->addGoldItem(24, 100);
        $result = $this->calculator->calculate();
        $this->assertArrayHasKey('items', $result['details']['gold']);
        $this->assertArrayHasKey('zakat', $result['details']['gold']);
    }

    public function testItReturnsDetailedBusinessCalculation()
    {
        $this->calculator->setBusinessAssets([
            'inventory'    => 10000,
            'receivables'  => 0,
            'cash_at_bank' => 0,
        ]);
        $result = $this->calculator->calculate();
        $this->assertArrayHasKey('assets', $result['details']['business']);
        $this->assertArrayHasKey('zakat', $result['details']['business']);
    }

    public function testItReturnsDetailedAgriculturalCalculation()
    {
        $this->calculator->setAgriculturalProducts([
            'wheat' => [
                'weight'       => 400,
                'is_irrigated' => true,
            ],
        ]);
        $result = $this->calculator->calculate();
        $this->assertArrayHasKey('products', $result['details']['agricultural']);
        $this->assertArrayHasKey('zakat', $result['details']['agricultural']);
    }
}
