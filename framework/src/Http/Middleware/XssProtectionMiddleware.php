<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Middleware;

use DJWeb\Framework\Config\Contracts\ConfigContract;
use HTMLPurifier;
use HTMLPurifier_Config;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class XssProtectionMiddleware implements MiddlewareInterface
{
    private HTMLPurifier $purifier;
private ConfigContract $config;
public function __construct(ConfigContract $config)
    {
        $this->config = $config;
$this->initializePurifier();
}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): \Psr\Http\Message\ResponseInterface
    {
        // Sprawdzenie czy ścieżka jest na whiteliście
        $path = $request->getUri()->getPath();
$whitelist = $this->config->get('purifier.whitelist', []);
if ($this->isWhitelisted($path, $whitelist)) {
            return $handler->handle($request);

}

        $parsedBody = $request->getParsedBody();
if (is_array($parsedBody)) {
            $cleanedBody = $this->purifyData($parsedBody);
$request = $request->withParsedBody($cleanedBody);

}

        $queryParams = $request->getQueryParams();
if (! empty($queryParams)) {
            $cleanedQuery = $this->purifyData($queryParams);
$request = $request->withQueryParams($cleanedQuery);

}

        return $handler->handle($request);
    }

    private function initializePurifier(): void
    {
        $config = HTMLPurifier_Config::createDefault();
// Podstawowa konfiguracja z configa
        $config->set('Core.Encoding', $this->config->get('purifier.encoding', 'UTF-8'));
$config->set('HTML.Doctype', $this->config->get('purifier.doctype', 'HTML 4.01 Transitional'));
$config->set('Cache.SerializerPath', $this->config->get('purifier.cache.path', sys_get_temp_dir()));
// Obsługa predefiniowanych zestawów reguł
        $settings = $this->config->get('purifier.settings.default', []);
foreach ($settings as $directive => $value) {
            $config->set($directive, $value);

}

        // Obsługa różnych profili czyszczenia
        $profiles = $this->config->get('purifier.profiles', []);
foreach ($profiles as $name => $rules) {
            $config->set("HTML.Allowed.{$name}", $rules['allowed'] ?? '');
if (isset($rules['custom'])) {
                foreach ($rules['custom'] as $directive => $value) {
                    $config->set("{$name}.{$directive}", $value);

                }

}

}

        $this->purifier = new HTMLPurifier($config);
    }

    private function purifyData($data, string $profile = 'default')
    {
        if (is_string($data)) {
            return $this->purifier->purify($data);

        }

        if (is_array($data)) {
            return array_map(fn ($value) => $this->purifyData($value, $profile), $data);

        }

        return $data;
    }

    private function isWhitelisted(string $path, array $whitelist): bool
    {
        foreach ($whitelist as $pattern) {
            if (preg_match('#^' . str_replace('*', '.*', $pattern) . '$#', $path)) {
                return true;

            }

        }
        return false;
    }

}
