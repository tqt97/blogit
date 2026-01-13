<?php

return [
    'cache' => [
        'use_tags' => env('POST_MODULE_CACHE_TAGS', null),
        'ttl' => env('POST_MODULE_CACHE_TTL', 3600),
        'prefix' => 'post:',
    ],

    'pagination' => [
        'default_per_page' => env('POST_MODULE_PER_PAGE', 15),
        'max_per_page' => env('POST_MODULE_MAX_PER_PAGE', 100),
    ],
];
