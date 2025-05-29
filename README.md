# Laravel Zakat Calculator

A comprehensive Laravel package for calculating Zakat on various assets including money, gold, silver, business assets, and agricultural products.

## Features

- Calculate Zakat on cash
- Calculate Zakat on gold and silver
- Calculate Zakat on business assets
- Calculate Zakat on agricultural products
- Support for different calculation methods (Hanafi, Shafi, Maliki, Hanbali)
- Configurable nisab thresholds
- Caching support for gold and silver prices
- Input validation and error handling
- Support for different currencies and weight units

## Installation

You can install the package via composer:

```bash
composer require tawfik/zakat-calculator
```

The package will automatically register its service provider.

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Tawfik\ZakatCalculator\ZakatServiceProvider" --tag="config"
```

This will create a `config/zakat.php` file in your config directory.

## Usage

### Basic Usage

```php
use Tawfik\ZakatCalculator\ZakatCalculator;

$calculator = new ZakatCalculator();

$result = $calculator
    ->setCash(1000)
    ->setGoldItems([
        ['weight' => 100, 'karat' => 24],
        ['weight' => 50, 'karat' => 18]
    ])
    ->setSilverWeight(500)
    ->setGoldPrice(50) // per gram
    ->setSilverPrice(0.5) // per gram
    ->calculate();
```

### Business Assets

```php
$calculator->setBusinessAssets([
    'inventory' => 5000,
    'receivables' => 2000,
    'cash_at_bank' => 10000,
    'cash_in_hand' => 2000
]);
```

### Agricultural Products

```php
$calculator->setAgriculturalProducts([
    [
        'value' => 10000,
        'irrigated' => true // 5% rate
    ],
    [
        'value' => 5000,
        'irrigated' => false // 10% rate
    ]
]);
```

### Different Calculation Methods

```php
$calculator->setCalculationMethod('hanafi'); // or 'shafi', 'maliki', 'hanbali'
```

## Configuration Options

The package provides several configuration options in `config/zakat.php`:

- Default gold and silver prices
- Nisab thresholds
- Currency settings
- Weight units
- Calculation precision
- Supported gold karats
- Cache settings
- Calculation methods
- Business assets settings
- Agricultural products rates

## Response Format

The `calculate()` method returns an array with the following structure:

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

## Error Handling

The package throws `InvalidInputException` for invalid inputs:

```php
use Tawfik\ZakatCalculator\Exceptions\InvalidInputException;

try {
    $calculator->setCash(-1000); // Will throw InvalidInputException
} catch (InvalidInputException $e) {
    // Handle the error
}
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email youremail@example.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 