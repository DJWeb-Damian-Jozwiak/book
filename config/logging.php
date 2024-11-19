<?php

return [
    'default' => 'database',

    'channels' => [
        'stack' => [
            'handler' => 'file',
            'path' => __DIR__.'/../storage/logs/app.log',
            'formatter' => 'text',
            'max_days' => 14
        ],

        'database' => [
            'handler' => 'database'
        ],

        'daily' => [
            'handler' => 'file',
            'path' =>  __DIR__.'/../storage/logs/daily.log',
            'formatter' => 'json',
            'max_days' => 7
        ]
    ]
];