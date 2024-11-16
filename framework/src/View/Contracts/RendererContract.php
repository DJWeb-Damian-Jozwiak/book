<?php

namespace DJWeb\Framework\View\Contracts;

interface RendererContract
{
    /**
     * @param string $template
     * @param array<string, mixed> $data
     * @return string
     */
    public function render(string $template, array $data = []): string;
    public function __construct(string $template_path, string $cache_path);

    public static function buildDefault(): RendererContract;

    public function clearCache(string $cache_path): void;
}