<?php

return [
    // Podstawowa konfiguracja
    'encoding' => 'UTF-8',
    'doctype' => 'HTML 4.01 Transitional',

    // Domyślne ustawienia
    'settings' => [
        'default' => [
            'HTML.Allowed' => 'p,b,i,strong,em,a[href],ul,ol,li,br,span',
            'AutoFormat.AutoParagraph' => true,
            'AutoFormat.RemoveEmpty' => true,
        ],
    ],

    // Profile czyszczenia dla różnych kontekstów
    'profiles' => [
        'basic' => [
            'allowed' => 'p,b,i,br',
        ],
        'advanced' => [
            'allowed' => 'p,b,i,strong,em,a[href|title],ul,ol,li,br,span,div,h1,h2,h3,table[border|cellpadding|cellspacing],tr,td,th',
            'custom' => [
                'AutoFormat.AutoParagraph' => true,
                'AutoFormat.RemoveEmpty' => true,
            ],
        ],
    ],

    // Whitelist ścieżek, które nie wymagają czyszczenia
    'whitelist' => [
        '/api/*',
        '/webhook/*',
        '/admin/content/*',
    ],
];