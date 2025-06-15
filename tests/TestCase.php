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
        $app['config']->set('zakat.default_prices', [
            'gold'   => 50.00,
            'silver' => 0.75,
        ]);

        $app['config']->set('zakat.nisab', [
            'gold'   => 85,
            'silver' => 595,
        ]);

        $app['config']->set('zakat.currency', [
            'code'     => 'USD',
            'symbol'   => '$',
            'position' => 'before',
        ]);

        $app['config']->set('zakat.weight_unit', 'gram');

        $app['config']->set('zakat.precision', 2);

        $app['config']->set('zakat.supported_karats', [24, 22, 21, 18, 14, 12, 10]);

        $app['config']->set('zakat.cache', [
            'enabled' => true,
            'ttl'     => 3600,
        ]);

        $app['config']->set('zakat.calculation_methods', [
            'default' => 'hanafi',
        ]);

        $app['config']->set('zakat.business', [
            'inventory'    => true,
            'receivables'  => true,
            'cash_at_bank' => true,
            'cash_in_hand' => true,
        ]);

        $app['config']->set('zakat.agricultural', [
            'irrigated_rate'     => 0.05,
            'non_irrigated_rate' => 0.1,
            'wasq_weights'       => [
                'wheat'          => 60,
                'barley'         => 60,
                'dates'          => 60,
                'raisins'        => 60,
                'rice'           => 60,
                'corn'           => 60,
                'millet'         => 60,
                'beans'          => 60,
                'lentils'        => 60,
                'chickpeas'      => 60,
                'peas'           => 60,
                'cotton'         => 60,
                'flax'           => 60,
                'sesame'         => 60,
                'olives'         => 60,
                'grapes'         => 60,
                'pomegranates'   => 60,
                'figs'           => 60,
                'almonds'        => 60,
                'pistachios'     => 60,
                'walnuts'        => 60,
                'hazelnuts'      => 60,
                'peanuts'        => 60,
                'sunflower'      => 60,
                'safflower'      => 60,
                'mustard'        => 60,
                'fenugreek'      => 60,
                'cumin'          => 60,
                'coriander'      => 60,
                'fennel'         => 60,
                'anise'          => 60,
                'caraway'        => 60,
                'cardamom'       => 60,
                'cloves'         => 60,
                'cinnamon'       => 60,
                'ginger'         => 60,
                'turmeric'       => 60,
                'black_pepper'   => 60,
                'white_pepper'   => 60,
                'red_pepper'     => 60,
                'green_pepper'   => 60,
                'paprika'        => 60,
                'chili'          => 60,
                'cayenne'        => 60,
                'nutmeg'         => 60,
                'mace'           => 60,
                'allspice'       => 60,
                'star_anise'     => 60,
                'cassia'         => 60,
                'bay_leaves'     => 60,
                'thyme'          => 60,
                'oregano'        => 60,
                'marjoram'       => 60,
                'sage'           => 60,
                'rosemary'       => 60,
                'basil'          => 60,
                'mint'           => 60,
                'parsley'        => 60,
                'dill'           => 60,
                'chives'         => 60,
                'tarragon'       => 60,
                'chervil'        => 60,
                'lavender'       => 60,
                'lemon_balm'     => 60,
                'lemon_grass'    => 60,
                'lemon_verbena'  => 60,
                'lemon_thyme'    => 60,
                'lemon_basil'    => 60,
                'lemon_mint'     => 60,
                'lemon_parsley'  => 60,
                'lemon_dill'     => 60,
                'lemon_chives'   => 60,
                'lemon_tarragon' => 60,
                'lemon_chervil'  => 60,
                'lemon_lavender' => 60,
            ],
        ]);
    }
}
