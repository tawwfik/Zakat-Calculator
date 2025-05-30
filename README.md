# Zakat Calculator for Laravel

A Laravel package for calculating zakat on money, gold, silver, business assets, and agricultural products.

## Installation

You can install the package via composer:

```bash
composer require tawwfik/zakat-calculator
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

After publishing the configuration file, you can customize the following settings in `config/zakat.php`:

### Default Prices
```php
'default_prices' => [
    'gold'   => 0.00, // Set your default gold price per gram
    'silver' => 0.00, // Set your default silver price per gram
],
```

### Nisab Thresholds
```php
'nisab' => [
    'gold'   => 85,  // grams of gold
    'silver' => 595, // grams of silver
],
```

### Currency Settings
```php
'currency' => [
    'code'     => 'SAR',    // Currency code
    'symbol'   => 'ر.س',      // Currency symbol
    'position' => 'before', // Symbol position: 'before' or 'after'
],
```

### Weight Unit
```php
'weight_unit' => 'gram', // 'gram' or 'ounce'
```

### Calculation Precision
```php
'precision' => 2, // Number of decimal places
```

### Supported Gold Karats
```php
'supported_karats' => [24, 22, 21, 18, 14, 12, 10],
```

### Cache Settings
```php
'cache' => [
    'enabled' => true,
    'ttl'     => 3600, // Cache time-to-live in seconds
],
```

### Calculation Methods
```php
'calculation_methods' => [
    'default' => 'hanafi', // Available: 'hanafi', 'shafi', 'maliki', 'hanbali'
],
```

### Business Assets
```php
'business' => [
    'inventory'    => true,    // Include inventory in calculation
    'receivables'  => true,    // Include receivables in calculation
    'cash_at_bank' => true,    // Include bank cash in calculation
    'cash_in_hand' => true,    // Include cash in hand in calculation
],
```

### Agricultural Products
```php
'agricultural' => [
    'irrigated_rate'     => 0.05, // 5% rate for irrigated crops
    'non_irrigated_rate' => 0.1,  // 10% rate for non-irrigated crops
],
```

## Testing

```bash
composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 