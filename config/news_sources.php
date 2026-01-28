<?php

return [
    'newsapi' => [
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('NEWSAPI_KEY'),
        'base_url' => env('NEWS_API_URL', 'https://newsapi.org'),
    ],

    'guardian' => [
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('GUARDIAN_KEY'),
        'base_url' => env('GUARDIAN_BASE_URL', 'https://content.guardianapis.com'),
    ],

    'nyt' => [
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('NYT_KEY'),
        'secret' => env('NYT_SECRET'),
        'base_url' => env('NYT_API_URL', 'https://api.nytimes.com'),
    ],
];
