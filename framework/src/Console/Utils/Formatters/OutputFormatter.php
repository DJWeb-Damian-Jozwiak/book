<?php

declare(strict_types=1);

namespace DJWeb\Framework\Console\Utils\Formatters;

use DJWeb\Framework\Container\Contracts\ContainerContract;

class OutputFormatter
{
    /**
     * @var array<string, OutputFormatterStyle>
     */
    private array $styles = [];

    public function __construct(
        private readonly ContainerContract $container
    ) {
        $this->initializeDefaultStyles();
        $this->container->set(self::class, $this);
    }

    public function withStyle(string $name, OutputFormatterStyle $style): void
    {
        $this->styles[$name] = $style;
    }

    public function getStyle(string $name): OutputFormatterStyle
    {
        return $this->styles[$name] ?? new NormalStyle();
    }

    public function format(string $message): string
    {
        return preg_replace_callback(
            '/<([a-z-]+)>(.*?)<\/[a-z-]+>/i',
            function ($matches) {
                $style = $this->getStyle($matches[1]);
                return $style->apply($matches[2]);
            },
            $message
        ) ?? '';
    }

    private function initializeDefaultStyles(): void
    {
        $this->withStyle('success', new SuccessStyle());
        $this->withStyle('info', new InfoStyle());
        $this->withStyle('warning', new WarningStyle());
        $this->withStyle('danger', new DangerStyle());
        $this->withStyle('normal', new NormalStyle());
    }
}
