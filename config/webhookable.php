<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'possible_events' => [
        'tradingMonth.*',
        'tradingMonth.created',
        'tradingMonth.deleted',
        'tradingMonth.updated',
        'ifeMilkExchangeValue.*',
        'ifeMilkExchangeValue.created',
        'ifeMilkExchangeValue.deleted',
        'ifeMilkExchangeValue.updated',
        'tradingMonthEod.*',
        'tradingMonthEod.created',
        'tradingMonthEod.deleted',
        'tradingMonthEod.updated',
        'commodity.*',
        'commodity.touched',
    ],
];
