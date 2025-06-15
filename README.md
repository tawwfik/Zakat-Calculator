# Zakat Calculator for Laravel

A comprehensive Laravel package for calculating zakat on various assets including money, gold, silver, business assets, and agricultural products. This package follows Islamic principles and supports multiple calculation methods.

## Features

- Calculate zakat on multiple asset types:
  - Cash and bank balances
  - Gold (supports multiple karats)
  - Silver
  - Business assets (inventory, receivables, cash)
  - Agricultural products
- Support for different calculation methods (Hanafi, Shafi'i, Maliki, Hanbali)
- Configurable nisab thresholds
- Real-time gold and silver price integration
- Detailed calculation breakdowns
- Caching support for better performance
- Comprehensive validation and error handling

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

#### Asset Setting Methods
- `setCash(float $cash)`: Set cash amount
- `setGoldItems(array $goldItems)`: Set gold items with weight and karat
- `setSilverWeight(float $silverWeight)`: Set silver weight
- `setGoldPrice(float $goldPrice)`: Set gold price per gram
- `setSilverPrice(float $silverPrice)`: Set silver price per gram
- `setBusinessAssets(array $assets)`: Set business assets
- `setAgriculturalProducts(array $products)`: Set agricultural products

#### Configuration Methods
- `setCalculationMethod(string $method)`: Set calculation method (hanafi, shafi, maliki, hanbali)
- `setNisabThreshold(float $threshold)`: Set custom nisab threshold
- `setCacheEnabled(bool $enabled)`: Enable/disable caching
- `setCacheTTL(int $ttl)`: Set cache time-to-live in seconds

#### Calculation Methods
- `calculate()`: Calculate total zakat
- `calculateCashZakat()`: Calculate zakat on cash only
- `calculateGoldZakat()`: Calculate zakat on gold only
- `calculateSilverZakat()`: Calculate zakat on silver only
- `calculateBusinessZakat()`: Calculate zakat on business assets only
- `calculateAgriculturalZakat()`: Calculate zakat on agricultural products only

### Calculation Result

The `calculate()` method returns an array with the following information:

```php
[
    'total_assets' => [
        'cash' => float,
        'gold' => float,
        'silver' => float,
        'business' => float,
        'agricultural' => float
    ],
    'nisab_value' => float,
    'is_eligible' => boolean,
    'total_zakat' => float,
    'details' => [
        'cash' => [
            'amount' => float,
            'zakat' => float
        ],
        'gold' => [
            'items' => array,
            'total_weight' => float,
            'total_value' => float,
            'zakat' => float
        ],
        'silver' => [
            'weight' => float,
            'value' => float,
            'zakat' => float
        ],
        'business' => [
            'assets' => array,
            'total_value' => float,
            'zakat' => float
        ],
        'agricultural' => [
            'products' => array,
            'total_value' => float,
            'zakat' => float
        ]
    ],
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

## Advanced Usage Examples

### Calculating Zakat for Gold Only
```php
$result = $calculator
    ->setGoldItems([
        ['weight' => 100, 'karat' => 24],
        ['weight' => 50, 'karat' => 18]
    ])
    ->setGoldPrice(100) // Price per gram
    ->calculateGoldZakat();
```

### Calculating Business Zakat
```php
$result = $calculator
    ->setBusinessAssets([
        'inventory' => 5000,
        'receivables' => 2000,
        'cash_at_bank' => 3000,
        'cash_in_hand' => 1000
    ])
    ->calculateBusinessZakat();
```

### Calculating Agricultural Zakat
```php
$result = $calculator
    ->setAgriculturalProducts([
        ['value' => 10000, 'irrigated' => true],  // Irrigated crops (5%)
        ['value' => 20000, 'irrigated' => false]  // Non-irrigated crops (10%)
    ])
    ->calculateAgriculturalZakat();
```

### Using Different Calculation Methods
```php
$result = $calculator
    ->setCalculationMethod('shafi')
    ->setCash(10000)
    ->setGoldItems([['weight' => 100, 'karat' => 24]])
    ->calculate();
```

## Error Handling

The package throws specific exceptions for different error cases:

- `InvalidInputException`: Thrown for invalid input values
- `InvalidKaratException`: Thrown for unsupported gold karats
- `ConfigurationException`: Thrown for missing or invalid configuration

Example error handling:

```php
use Tawfik\ZakatCalculator\Exceptions\InvalidInputException;

try {
    $result = $calculator->setCash(-1000)->calculate();
} catch (InvalidInputException $e) {
    return response()->json(['error' => $e->getMessage()], 400);
}
```

## Testing

Run the tests using:

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email security@tawfik.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 