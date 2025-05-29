<?php
namespace Tawfik\ZakatCalculator;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\ServiceProvider;

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
                $app->make(CacheRepository::class),
                $app['config']
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/zakat.php' => config_path('zakat.php'),
        ], 'config');
    }
}
