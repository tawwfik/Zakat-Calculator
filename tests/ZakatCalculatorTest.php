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
    protected ZakatCalculator $calculator;
    protected CacheRepository $cache;
    protected ConfigRepository $config;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cache  = Mockery::mock(CacheRepository::class);
        $this->config = Mockery::mock(ConfigRepository::class);

        // Set up default config values
        $this->config->shouldReceive('get')
            ->with('zakat.calculation_methods.default', 'hanafi')
            ->andReturn('hanafi');
        $this->config->shouldReceive('get')
            ->with('zakat.supported_karats')
            ->andReturn([24, 22, 21, 18]);
        $this->config->shouldReceive('get')
            ->with('zakat.agricultural.irrigated_rate')
            ->andReturn(0.05);
        $this->config->shouldReceive('get')
            ->with('zakat.agricultural.non_irrigated_rate')
            ->andReturn(0.1);
        $this->config->shouldReceive('get')
            ->with('zakat.nisab.gold')
            ->andReturn(87.48);
        $this->config->shouldReceive('get')
            ->with('zakat.precision', 2)
            ->andReturn(2);
        $this->config->shouldReceive('get')
            ->with('zakat.cache.enabled')
            ->andReturn(false);
        $this->config->shouldReceive('get')
            ->with('zakat.default_prices.gold')
            ->andReturn(50.0);
        $this->config->shouldReceive('get')
            ->with('zakat.default_prices.silver')
            ->andReturn(0.5);

        $this->calculator = new ZakatCalculator($this->cache, $this->config);
    }

    public function testBasicCalculation()
    {
        $result = $this->calculator
            ->setCash(1000)
            ->setGoldItems([
                ['weight' => 100, 'karat' => 24],
                ['weight' => 50, 'karat' => 18],
            ])
            ->setSilverWeight(500)
            ->calculate();

        $this->assertEquals(1000, $result['cash']);
        $this->assertEquals(6875.0, $result['gold_value']);  // (100 * 50) + (50 * 18/24 * 50)
        $this->assertEquals(250.0, $result['silver_value']); // 500 * 0.5
        $this->assertEquals(8125.0, $result['total']);
        $this->assertEquals(4374.0, $result['nisab']); // 87.48 * 50
        $this->assertTrue($result['zakat_due']);
        $this->assertEquals(203.13, $result['zakat_amount']); // 8125 * 0.025
    }

    public function testNegativeCashThrowsException()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setCash(-100);
    }

    public function testInvalidGoldKaratThrowsException()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setGoldItems([
            ['weight' => 100, 'karat' => 14],
        ]);
    }

    public function testBusinessAssetsCalculation()
    {
        $result = $this->calculator
            ->setBusinessAssets([
                'inventory'    => 5000,
                'receivables'  => 2000,
                'cash_at_bank' => 3000,
            ])
            ->calculate();

        $this->assertEquals(10000.0, $result['business_value']);
    }

    public function testAgriculturalProductsCalculation()
    {
        $result = $this->calculator
            ->setAgriculturalProducts([
                ['value' => 10000, 'irrigated' => true],
                ['value' => 20000, 'irrigated' => false],
            ])
            ->calculate();

        $this->assertEquals(2500.0, $result['agricultural_value']); // (10000 * 0.05) + (20000 * 0.1)
    }

    public function testDifferentCalculationMethods()
    {
        $this->calculator->setCalculationMethod('shafi');
        $result = $this->calculator->calculate();
        $this->assertEquals('shafi', $result['calculation_method']);
    }

    public function testInvalidCalculationMethodThrowsException()
    {
        $this->expectException(InvalidInputException::class);
        $this->calculator->setCalculationMethod('invalid');
    }

    public function testBelowNisabThreshold()
    {
        $result = $this->calculator
            ->setCash(1000)
            ->setGoldItems([
                ['weight' => 10, 'karat' => 24],
            ])
            ->calculate();

        $this->assertEquals(1500.0, $result['total']); // 1000 + (10 * 50)
        $this->assertEquals(4374.0, $result['nisab']); // 87.48 * 50
        $this->assertFalse($result['zakat_due']);
        $this->assertEquals(0.0, $result['zakat_amount']);
    }
}
