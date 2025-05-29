# Zakat Calculator for Laravel

A Laravel package for calculating zakat on money, gold, silver, business assets, and agricultural products.

## Installation

You can install the package via composer:

```bash
composer require tawfik/zakat-calculator
```

The package will automatically register its service provider.

### Publishing Configuration

You can publish the configuration file using:

```bash
php artisan vendor:publish --provider="Tawfik\ZakatCalculator\ZakatServiceProvider" --tag="config"
```

This will create a `config/zakat.php` file in your config directory.

## Usage

### Basic Usage

```php
use Tawfik\ZakatCalculator\ZakatCalculator;

class ZakatController extends Controller
{
    protected $calculator;

    public function __construct(ZakatCalculator $calculator)
    {
        $this->calculator = $calculator;
    }

    public function calculate(Request $request)
    {
        $result = $this->calculator
            ->setCash($request->cash)
            ->setGoldItems([
                ['weight' => 100, 'karat' => 24],
                ['weight' => 50, 'karat' => 18]
            ])
            ->setSilverWeight(500)
            ->setBusinessAssets([
                'inventory' => 5000,
                'receivables' => 2000,
                'cash_at_bank' => 3000
            ])
            ->setAgriculturalProducts([
                ['value' => 10000, 'irrigated' => true],
                ['value' => 20000, 'irrigated' => false]
            ])
            ->calculate();

        return response()->json($result);
    }
}
```

### Available Methods

- `setCash(float $cash)`: Set cash amount
- `setGoldItems(array $goldItems)`: Set gold items with weight and karat
- `setSilverWeight(float $silverWeight)`: Set silver weight
- `setGoldPrice(float $goldPrice)`: Set gold price per gram
- `setSilverPrice(float $silverPrice)`: Set silver price per gram
- `setBusinessAssets(array $assets)`: Set business assets
- `setAgriculturalProducts(array $products)`: Set agricultural products
- `setCalculationMethod(string $method)`: Set calculation method (hanafi, shafi, maliki, hanbali)

### Calculation Result

The `calculate()` method returns an array with the following information:

```php
[
    'cash' => float,
    'gold_value' => float,
    'silver_value' => float,
    'business_value' => float,
    'agricultural_value' => float,
    'total' => float,
    'nisab' => float,
    'zakat_due' => boolean,
    'zakat_amount' => float,
    'calculation_method' => string
]
```

## Configuration

The package configuration file (`config/zakat.php`) includes the following settings:

- Default prices for gold and silver
- Nisab thresholds
- Supported gold karats
- Agricultural rates
- Calculation methods
- Cache settings
- Precision settings

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 