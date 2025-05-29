<?php
namespace Tawfik\ZakatCalculator\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Tawfik\ZakatCalculator\ZakatServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ZakatServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Setup zakat configuration
        $app['config']->set('zakat', [
            'default_prices'      => [
                'gold'   => 50.00,
                'silver' => 0.50,
            ],
            'nisab'               => [
                'gold'   => 85,
                'silver' => 595,
            ],
            'currency'            => [
                'code'     => 'USD',
                'symbol'   => '$',
                'position' => 'before',
            ],
            'weight_unit'         => 'gram',
            'precision'           => 2,
            'supported_karats'    => [24, 22, 21, 18, 14, 12, 10],
            'cache'               => [
                'enabled' => true,
                'ttl'     => 3600,
            ],
            'calculation_methods' => [
                'default' => 'hanafi',
            ],
            'business'            => [
                'inventory'    => true,
                'receivables'  => true,
                'cash_at_bank' => true,
                'cash_in_hand' => true,
            ],
            'agricultural'        => [
                'irrigated_rate'     => 0.05,
                'non_irrigated_rate' => 0.1,
            ],
        ]);
    }
}
