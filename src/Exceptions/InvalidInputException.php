<?php
namespace Tawfik\ZakatCalculator\Exceptions;

use Exception;

class InvalidInputException extends Exception
{
    public static function negativeValue(string $field): self
    {
        return new self("{$field} cannot be negative");
    }

    public static function invalidKarat(int $karat): self
    {
        return new self("Unsupported gold karat: {$karat}");
    }

    public static function invalidWeightUnit(string $unit): self
    {
        return new self("Invalid weight unit: {$unit}. Supported units are: gram, ounce");
    }

    public static function invalidCalculationMethod(string $method): self
    {
        return new self("Unsupported calculation method: {$method}");
    }
}
