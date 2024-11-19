<?php

return [
    'default' => env('VIEW_ENGINE', 'blade'),

    'engines' => [
        'twig' => [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/twig',
                'cache_path' => __DIR__ . '/../storage/cache/twig',
            ]
        ],
        'blade' => [
            'paths' => [
                'template_path' => __DIR__ . '/../resources/views/blade',
                'cache_path' => __DIR__ . '/../storage/cache/blade',
            ],
            'components' => [
                'namespace' => '\\App\\View\\Components\\',
            ]
        ]
    ],
];