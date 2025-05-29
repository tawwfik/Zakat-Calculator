<?php

namespace Tawfik\ZakatCalculator\Facades;

use Illuminate\Support\Facades\Facade;

class Zakat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'zakat-calculator';
    }
}
