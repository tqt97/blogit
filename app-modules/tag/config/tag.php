<?php

return [
    'cache' => [
        'use_tags' => env('TAG_MODULE_CACHE_TAGS', false),
        'ttl' => env('TAG_MODULE_CACHE_TTL', 3600),
        'prefix' => 'tag:',
    ],

    'pagination' => [
        'default_per_page' => env('TAG_MODULE_PER_PAGE', 15),
        'max_per_page' => env('TAG_MODULE_MAX_PER_PAGE', 100),
    ],

    'slug' => [
        'max_length' => 255,
        'regex' => '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
    ],
];
