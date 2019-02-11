<?php

return [
    'mtn' => [
        'primary_key' => env('MTN_PRIMARY_KEY'),
        'secondary_key' => env('MTN_SECONDARY_KEY'),
        'user_id' => env('MTN_USER_ID'),
        'user_key' => env('MTN_USER_KEY'),
        'callback_url' => env('MOBILE_MONEY_CALLBACK_URL')
    ],
];