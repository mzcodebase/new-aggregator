<?php

return [
    'newsapi' => [
        'enabled' => env('NEWSAPI_ENABLED', true),
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('NEWSAPI_KEY'),
        'base_url' => env('NEWS_API_URL', 'https://newsapi.org'),
    ],

    'guardian' => [
        'enabled' => env('GUARDIAN_ENABLED', true),
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('GUARDIAN_KEY'),
        'base_url' => env('GUARDIAN_BASE_URL', 'https://content.guardianapis.com'),
    ],

    'nyt' => [
        'enabled' => env('NYT_ENABLED', true),
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('NYT_KEY'),
        'secret' => env('NYT_SECRET'),
        'base_url' => env('NYT_API_URL', 'https://api.nytimes.com/svc'),
    ],

    'bbc' => [
        'enabled' => env('BBC_ENABLED', true),
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'base_url' => env('BBC_API_URL', 'https://bbc-news-api.vercel.app'),
        'language' => env('BBC_LANGUAGE', 'english'),
    ],

    'opennews' => [
        'enabled' => env('OPENNEWS_ENABLED', false),
        'rate_limit' => [
            'max_requests' => 10,
            'per_minutes' => 1,
        ],
        'base_url' => env('OPENNEWS_API_URL', 'https://opennewsapi.herokuapp.com'),
        'articles_endpoint' => env('OPENNEWS_ARTICLES_ENDPOINT', 'api/articles'),
    ],

    'newscred' => [
        'enabled' => env('NEWSCRED_ENABLED', false),
        'rate_limit' => [
            'max_requests' => 5,
            'per_minutes' => 1,
        ],
        'key' => env('NEWSCRED_KEY'),
        'base_url' => env('NEWSCRED_BASE_URL', 'https://api.newscred.com'),
    ],
];
