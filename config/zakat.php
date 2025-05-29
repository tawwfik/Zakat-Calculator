<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Zakat Calculator Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the default configuration for the zakat calculator.
    |
    */

    // Default gold and silver prices (per gram)
    'default_prices'      => [
        'gold'   => 0.00, // Set your default gold price
        'silver' => 0.00, // Set your default silver price
    ],

    // Nisab threshold in grams
    'nisab'               => [
        'gold'   => 85,  // grams of gold
        'silver' => 595, // grams of silver
    ],

    // Currency settings
    'currency'            => [
        'code'     => 'USD',
        'symbol'   => '$',
        'position' => 'before', // 'before' or 'after'
    ],

                                     // Weight units
    'weight_unit'         => 'gram', // 'gram' or 'ounce'

    // Calculation precision
    'precision'           => 2,

    // Supported gold karats
    'supported_karats'    => [24, 22, 21, 18, 14, 12, 10],

    // Cache settings
    'cache'               => [
        'enabled' => true,
        'ttl'     => 3600, // Time to live in seconds
    ],

    // Supported calculation methods
    'calculation_methods' => [
        'default' => 'hanafi', // 'hanafi', 'shafi', 'maliki', 'hanbali'
    ],

    // Business assets calculation
    'business'            => [
        'inventory'    => true,
        'receivables'  => true,
        'cash_at_bank' => true,
        'cash_in_hand' => true,
    ],

    // Agricultural products
    'agricultural'        => [
        'irrigated_rate'     => 0.05, // 5%
        'non_irrigated_rate' => 0.1,  // 10%
    ],
];
