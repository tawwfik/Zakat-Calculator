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
        'code'     => 'SAR',
        'symbol'   => 'ر.س',
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
        'irrigated_rate'     => 0.05, // 5% for irrigated crops
        'non_irrigated_rate' => 0.1,  // 10% for non-irrigated crops

        // Wasq weights in kg for different crop types
        'wasq_weights'       => [
            'default'              => 60, // Default weight per wasq in kg
            'wheat'                => 60, // Wheat weight per wasq
            'barley'               => 60, // Barley weight per wasq
            'dates'                => 60, // Dates weight per wasq
            'raisins'              => 60, // Raisins weight per wasq
            'rice'                 => 60, // Rice weight per wasq
            'corn'                 => 60, // Corn weight per wasq
            'millet'               => 60, // Millet weight per wasq
            'beans'                => 60, // Beans weight per wasq
            'lentils'              => 60, // Lentils weight per wasq
            'chickpeas'            => 60, // Chickpeas weight per wasq
            'peas'                 => 60, // Peas weight per wasq
            'cotton'               => 60, // Cotton weight per wasq
            'flax'                 => 60, // Flax weight per wasq
            'sesame'               => 60, // Sesame weight per wasq
            'olives'               => 60, // Olives weight per wasq
            'grapes'               => 60, // Grapes weight per wasq
            'pomegranates'         => 60, // Pomegranates weight per wasq
            'figs'                 => 60, // Figs weight per wasq
            'almonds'              => 60, // Almonds weight per wasq
            'pistachios'           => 60, // Pistachios weight per wasq
            'walnuts'              => 60, // Walnuts weight per wasq
            'hazelnuts'            => 60, // Hazelnuts weight per wasq
            'peanuts'              => 60, // Peanuts weight per wasq
            'sunflower'            => 60, // Sunflower weight per wasq
            'safflower'            => 60, // Safflower weight per wasq
            'mustard'              => 60, // Mustard weight per wasq
            'fenugreek'            => 60, // Fenugreek weight per wasq
            'cumin'                => 60, // Cumin weight per wasq
            'coriander'            => 60, // Coriander weight per wasq
            'fennel'               => 60, // Fennel weight per wasq
            'anise'                => 60, // Anise weight per wasq
            'caraway'              => 60, // Caraway weight per wasq
            'cardamom'             => 60, // Cardamom weight per wasq
            'cloves'               => 60, // Cloves weight per wasq
            'cinnamon'             => 60, // Cinnamon weight per wasq
            'ginger'               => 60, // Ginger weight per wasq
            'turmeric'             => 60, // Turmeric weight per wasq
            'black_pepper'         => 60, // Black Pepper weight per wasq
            'white_pepper'         => 60, // White Pepper weight per wasq
            'red_pepper'           => 60, // Red Pepper weight per wasq
            'green_pepper'         => 60, // Green Pepper weight per wasq
            'paprika'              => 60, // Paprika weight per wasq
            'chili'                => 60, // Chili weight per wasq
            'cayenne'              => 60, // Cayenne weight per wasq
            'nutmeg'               => 60, // Nutmeg weight per wasq
            'mace'                 => 60, // Mace weight per wasq
            'allspice'             => 60, // Allspice weight per wasq
            'star_anise'           => 60, // Star Anise weight per wasq
            'cassia'               => 60, // Cassia weight per wasq
            'bay_leaves'           => 60, // Bay Leaves weight per wasq
            'thyme'                => 60, // Thyme weight per wasq
            'oregano'              => 60, // Oregano weight per wasq
            'marjoram'             => 60, // Marjoram weight per wasq
            'sage'                 => 60, // Sage weight per wasq
            'rosemary'             => 60, // Rosemary weight per wasq
            'basil'                => 60, // Basil weight per wasq
            'mint'                 => 60, // Mint weight per wasq
            'parsley'              => 60, // Parsley weight per wasq
            'dill'                 => 60, // Dill weight per wasq
            'chives'               => 60, // Chives weight per wasq
            'tarragon'             => 60, // Tarragon weight per wasq
            'chervil'              => 60, // Chervil weight per wasq
            'lavender'             => 60, // Lavender weight per wasq
            'lemon_balm'           => 60, // Lemon Balm weight per wasq
            'lemon_grass'          => 60, // Lemon Grass weight per wasq
            'lemon_verbena'        => 60, // Lemon Verbena weight per wasq
            'lemon_thyme'          => 60, // Lemon Thyme weight per wasq
            'lemon_basil'          => 60, // Lemon Basil weight per wasq
            'lemon_mint'           => 60, // Lemon Mint weight per wasq
            'lemon_parsley'        => 60, // Lemon Parsley weight per wasq
            'lemon_dill'           => 60, // Lemon Dill weight per wasq
            'lemon_chives'         => 60, // Lemon Chives weight per wasq
            'lemon_tarragon'       => 60, // Lemon Tarragon weight per wasq
            'lemon_chervil'        => 60, // Lemon Chervil weight per wasq
            'lemon_lavender'       => 60, // Lemon Lavender weight per wasq
            'lemon_lemon_balm'     => 60, // Lemon Lemon Balm weight per wasq
            'lemon_lemon_grass'    => 60, // Lemon Lemon Grass weight per wasq
            'lemon_lemon_verbena'  => 60, // Lemon Lemon Verbena weight per wasq
            'lemon_lemon_thyme'    => 60, // Lemon Lemon Thyme weight per wasq
            'lemon_lemon_basil'    => 60, // Lemon Lemon Basil weight per wasq
            'lemon_lemon_mint'     => 60, // Lemon Lemon Mint weight per wasq
            'lemon_lemon_parsley'  => 60, // Lemon Lemon Parsley weight per wasq
            'lemon_lemon_dill'     => 60, // Lemon Lemon Dill weight per wasq
            'lemon_lemon_chives'   => 60, // Lemon Lemon Chives weight per wasq
            'lemon_lemon_tarragon' => 60, // Lemon Lemon Tarragon weight per wasq
            'lemon_lemon_chervil'  => 60, // Lemon Lemon Chervil weight per wasq
            'lemon_lemon_lavender' => 60, // Lemon Lemon Lavender weight per wasq
        ],
    ],
];
