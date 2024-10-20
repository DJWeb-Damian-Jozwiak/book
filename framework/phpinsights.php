<?php

declare(strict_types=1);

use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use SlevomatCodingStandard\Sniffs\TypeHints\DisallowMixedTypeHintSniff;

return [

    'preset' => 'default',

    'exclude' => [
        'vendor',
        'node_modules',
        'tests',
        'helpers',
    ],

    'add' => [],

    'remove' => [
        //@TODO remove next 3 after insights is compatible with php8.4
        PhpCsFixer\Fixer\Basic\BracesFixer::class,
        PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer::class,
        PropertyDeclarationSniff::class,
        PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer::class,
        SlevomatCodingStandard\Sniffs\Classes\ForbiddenPublicPropertySniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ParameterTypeHintSniff::class,
        SlevomatCodingStandard\Sniffs\TypeHints\ReturnTypeHintSniff::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenGlobals::class,
        NunoMaduro\PhpInsights\Domain\Insights\ForbiddenNormalClasses::class,
        DisallowMixedTypeHintSniff::class,
    ],

    'config' => [
        LineLengthSniff::class => [
            'lineLimit' => 120,
            'absoluteLineLimit' => 0,
        ],
    ],
];
