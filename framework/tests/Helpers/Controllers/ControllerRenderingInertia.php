<?php

declare(strict_types=1);

namespace Tests\Helpers\Controllers;

use DJWeb\Framework\Routing\Attributes\Route;
use DJWeb\Framework\Routing\Controller;
use DJWeb\Framework\View\Inertia\Inertia;
use Psr\Http\Message\ResponseInterface;

class ControllerRenderingInertia extends Controller
{
    #[Route('/inertia/index', 'GET')]
    public function index(): ResponseInterface
    {
        return Inertia::render('InertiaRendering.vue', [
            'test' => 'test'
        ]);
    }

    #[Route('/inertia/shared', 'GET')]
    public function shared(): ResponseInterface
    {
        Inertia::share('sharedProp', 'sharedValue');

        return Inertia::render('InertiaRendering.vue', [
            'localProp' => 'localValue'
        ]);
    }

    #[Route('/inertia/redirect', 'GET')]
    public function redirect(): ResponseInterface
    {
        return Inertia::render('InertiaRendering.vue', [
            'localProp' => 'localValue'
        ]);
    }

    #[Route('/inertia/nested', 'GET')]
    public function nested(): ResponseInterface
    {
        Inertia::withRootView('inertia.blade.php');
        Inertia::share(['nestedProp'],  [
            'nestedProp' => [
                'data' => 'nestedValue',
                'array' => ['item1', 'item2'],
                'object' => (object)[
                    'key1' => 'value1',
                    'key2' => 'value2'
                ]
            ]
        ]);
        return Inertia::render('InertiaRendering.vue',);
    }

    #[Route('/inertia/custom-props', 'GET')]
    public function customProps(): ResponseInterface
    {
        return Inertia::render('InertiaRendering.vue', [
            'customProp' => 'customValue',
            'timestamp' => time(),
            'nested' => [
                'key' => 'value'
            ]
        ]);
    }
}