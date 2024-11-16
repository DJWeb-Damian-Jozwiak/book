<?php

declare(strict_types=1);

namespace DJWeb\Framework\View\Contracts;

interface RendererContract
{
public function __construct(string $template_path, string $cache_path);
    /**
     * namespace DJWeb\Framework\View\Contracts;
     *
     * interface RendererContract
     * {
     *
     * @param string $template
     * @param array<string, mixed> $data
     *
     * @return string
     *
     *
     * namespace DJWeb\Framework\View\Contracts;
     *
     * interface RendererContract
     * {
     */
    public function render(string $template, array $data = []): string;
public static function buildDefault(): RendererContract;
public function clearCache(string $cache_path): void;

}
