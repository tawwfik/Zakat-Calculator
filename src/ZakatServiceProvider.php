<?php
namespace Tawfik\ZakatCalculator;

use Illuminate\Support\ServiceProvider;
use Tawfik\ZakatCalculator\ZakatCalculator;

class ZakatServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/zakat.php', 'zakat'
        );

        $this->app->singleton(ZakatCalculator::class, function ($app) {
            return new ZakatCalculator(
                $app['cache.store'],
                $app['config']
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/zakat.php' => config_path('zakat.php'),
            ], 'config');
        }
    }
}
