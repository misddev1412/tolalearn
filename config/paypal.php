<?php
return [
    'client_id'                 => env('PAYPAL_CLIENT_ID','AWLs0QLBEHeP52fyNyb2xy1S_aKJwTz1ErvICyI_QZGWBtj3fAxO2VIvJWcU6xByQn0KWh5LnZN7YZUG'),
    'secret'                    => env('PAYPAL_SECRET','EESbquKV_PrbenXjytToRYeOUtwGwkrNDd7jl1-zm9c05Tf_LzQ095XNLYm3Rnj3CAUeZo75o2EqtpPf'),
    'currency'                  => env('PAYPAL_CURRENCY','USD'),
    'settings'                  => array(
        'mode'                  => env('PAYPAL_MODE','sandbox'),
        'http.ConnectionTimeOut'=> 30,
        'log.LogEnabled'        => true,
        'log.FileName'          => storage_path() . '/logs/paypal.log',
        'log.LogLevel'          => 'ERROR'
    ),
];
