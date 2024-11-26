<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Inertia;

use DJWeb\Framework\Config\Config;
use DJWeb\Framework\Http\Response;
use DJWeb\Framework\Http\Stream;
use Psr\Http\Message\ResponseInterface;

class Inertia
{
    /**
     * @var array<string, mixed>
     */
    private static array $sharedProps = [];

    /**
     * @var array<string, mixed>
     */
    private static array $props = [];
    private static string $rootView = 'inertia.blade.php';

    public function head(): string
    {
        if (! $this->isInertiaRequest()) {
            $page = $this->getPage();

            return implode("\n", array_filter([
                '<title>' . ($page['props']['title'] ?? Config::get('app.name', 'App')) . '</title>',
                '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">',
                sprintf(
                    '<script>window.page = %s;</script>',
                    json_encode($page)
                ),
            ]));
        }

        return '';
    }

    /**
     * @param string $component
     * @param array<string, mixed> $props
     *
     * @return ResponseInterface
     */
    public static function render(string $component, array $props = []): ResponseInterface
    {
        $responseFactory = new ResponseFactory();
        self::$props = array_merge(self::$sharedProps, $props);

        $page = [
            'component' => $component,
            'props' => self::$props,
            'url' => $_SERVER['REQUEST_URI'] ?? '/',
            'version' => '1.0',
        ];

        return $responseFactory->createResponse($page);
    }

    public static function share(string|array $key, mixed $value = null): void
    {
        if (is_array($key)) {
            self::$sharedProps = array_merge(self::$sharedProps, $key);
        } else {
            self::$sharedProps[$key] = $value;
        }
    }

    public static function getRootView(): string
    {
        return self::$rootView;
    }

    public static function withRootView(string $view): void
    {
        self::$rootView = $view;
    }


    public static function location(string $url): ResponseInterface
    {
        $headers = [
            'X-Inertia-Location' => $url,
            'Vary' => ['Accept', 'X-Requested-With']
        ];

        return new Response(
            headers: $headers,
            body: new Stream()->withContent(''),
            status: 409
        );
    }

    public static function locationJs(string $url): void
    {
        echo sprintf(
            '<script>window.location.href = "%s";</script>',
            htmlspecialchars($url, ENT_QUOTES)
        );
        exit;
    }

    /**
     * @return array<string, mixed>
     */
    private function getPage(): array
    {
        return [
            'component' => $this->component ?? '',
            'props' => self::$props,
            'url' => $_SERVER['REQUEST_URI'] ?? '/',
            'version' => 1.0,
        ];
    }

    private function isInertiaRequest(): bool
    {
        return isset($_SERVER['HTTP_X_INERTIA']) && $_SERVER['HTTP_X_INERTIA'] === 'true';
    }
}
