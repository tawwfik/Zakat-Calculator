#!/bin/bash

PACKAGE_DIR="/home/taw/Desktop/laravel-packages/tawfik/zakat-calculator"
VENDOR_NAMESPACE="Tawfik"
PACKAGE_NAMESPACE="ZakatCalculator"

echo "Creating Laravel package structure at $PACKAGE_DIR..."

# إنشاء المجلدات
mkdir -p $PACKAGE_DIR/src/Facades
mkdir -p $PACKAGE_DIR/config
mkdir -p $PACKAGE_DIR/src

# composer.json
cat > $PACKAGE_DIR/composer.json <<EOL
{
    "name": "tawfik/zakat-calculator",
    "description": "Laravel package to calculate zakat on money, gold, and silver",
    "authors": [
        {
            "name": "Tawfik",
            "email": "youremail@example.com"
        }
    ],
    "autoload": {
        "psr-4": {
            "$VENDOR_NAMESPACE\\\\$PACKAGE_NAMESPACE\\\\": "src/"
        }
    },
    "extra": {
        "laravel": {
            "providers": [
                "$VENDOR_NAMESPACE\\\\$PACKAGE_NAMESPACE\\\\ZakatServiceProvider"
            ],
            "aliases": {
                "Zakat": "$VENDOR_NAMESPACE\\\\$PACKAGE_NAMESPACE\\\\Facades\\\\Zakat"
            }
        }
    },
    "require": {
        "php": "^7.4|^8.0",
        "illuminate/support": "^8.0|^9.0|^10.0"
    },
    "minimum-stability": "dev",
    "prefer-stable": true
}
EOL

# ZakatCalculator.php (Service)
cat > $PACKAGE_DIR/src/ZakatCalculator.php <<EOL
<?php

namespace $VENDOR_NAMESPACE\\$PACKAGE_NAMESPACE;

class ZakatCalculator
{
    protected \$cash = 0;
    protected \$goldItems = [];
    protected \$silverWeight = 0;
    protected \$goldPrice = 0;
    protected \$silverPrice = 0;

    public function setCash(float \$cash)
    {
        \$this->cash = \$cash;
        return \$this;
    }

    public function setGoldItems(array \$goldItems)
    {
        \$this->goldItems = \$goldItems;
        return \$this;
    }

    public function setSilverWeight(float \$silverWeight)
    {
        \$this->silverWeight = \$silverWeight;
        return \$this;
    }

    public function setGoldPrice(float \$goldPrice)
    {
        \$this->goldPrice = \$goldPrice;
        return \$this;
    }

    public function setSilverPrice(float \$silverPrice)
    {
        \$this->silverPrice = \$silverPrice;
        return \$this;
    }

    public function calculate()
    {
        // حساب قيمة الذهب
        \$goldValue = 0;
        foreach (\$this->goldItems as \$item) {
            \$weight = \$item['weight'] ?? 0;
            \$karat = \$item['karat'] ?? 24;
            \$pureWeight = \$weight * (\$karat / 24);
            \$goldValue += \$pureWeight * \$this->goldPrice;
        }

        // حساب قيمة الفضة
        \$silverValue = \$this->silverWeight * \$this->silverPrice;

        // المجموع الكلي
        \$total = \$this->cash + \$goldValue + \$silverValue;

        // النصاب (85 جرام ذهب × سعر الجرام)
        \$nisab = 85 * \$this->goldPrice;

        // التحقق من بلوغ النصاب
        \$zakatDue = \$total >= \$nisab;

        // حساب الزكاة
        \$zakatAmount = \$zakatDue ? \$total * 0.025 : 0;

        return [
            'cash' => round(\$this->cash, 2),
            'gold_value' => round(\$goldValue, 2),
            'silver_value' => round(\$silverValue, 2),
            'total' => round(\$total, 2),
            'nisab' => round(\$nisab, 2),
            'zakat_due' => \$zakatDue,
            'zakat_amount' => round(\$zakatAmount, 2),
        ];
    }
}
EOL

# Facade Zakat.php
cat > $PACKAGE_DIR/src/Facades/Zakat.php <<EOL
<?php

namespace $VENDOR_NAMESPACE\\$PACKAGE_NAMESPACE\\Facades;

use Illuminate\Support\Facades\Facade;

class Zakat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zakat-calculator';
    }
}
EOL

# ServiceProvider
cat > $PACKAGE_DIR/src/ZakatServiceProvider.php <<EOL
<?php

namespace $VENDOR_NAMESPACE\\$PACKAGE_NAMESPACE;

use Illuminate\Support\ServiceProvider;

class ZakatServiceProvider extends ServiceProvider
{
    public function register()
    {
        \$this->app->singleton('zakat-calculator', function (\$app) {
            return new ZakatCalculator();
        });

        // لنشر ملف الإعداد إذا أردت مستقبلاً
        \$this->mergeConfigFrom(__DIR__ . '/../config/zakat.php', 'zakat');
    }

    public function boot()
    {
        // نشر ملف الإعداد
        \$this->publishes([
            __DIR__ . '/../config/zakat.php' => config_path('zakat.php'),
        ], 'config');
        
        // لا يتم تحميل أي Routes
    }
}
EOL

# ملف الإعداد config/zakat.php
cat > $PACKAGE_DIR/config/zakat.php <<EOL
<?php

return [
    'gold_nisab' => 85,
    'silver_nisab' => 595,
    'zakat_rate' => 0.025,
];
EOL

echo "Laravel Zakat Calculator package structure created successfully at $PACKAGE_DIR"
