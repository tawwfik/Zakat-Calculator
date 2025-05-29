<?php

namespace Tawfik\ZakatCalculator;

use Illuminate\Support\ServiceProvider;

class ZakatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('zakat-calculator', function ($app) {
            return new ZakatCalculator();
        });

        // لنشر ملف الإعداد إذا أردت مستقبلاً
        $this->mergeConfigFrom(__DIR__ . '/../config/zakat.php', 'zakat');
    }

    public function boot()
    {
        // نشر ملف الإعداد
        $this->publishes([
            __DIR__ . '/../config/zakat.php' => config_path('zakat.php'),
        ], 'config');
        
        // لا يتم تحميل أي Routes
    }
}
