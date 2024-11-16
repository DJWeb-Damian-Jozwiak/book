<?php

declare(strict_types=1);

namespace DJWeb\Framework\View;

use DJWeb\Framework\Http\Response;
use DJWeb\Framework\View\Contracts\RendererContract;
use DJWeb\Framework\View\Contracts\ViewContract;
use Psr\Http\Message\ResponseInterface;

class View extends Response implements ViewContract
{
    /**
     * @var array<string, mixed>
     */
    public protected(set) array $data = [];

    public function __construct(
        public protected(set) string $template,
        protected RendererContract $renderer
    ) {
        parent::__construct();
    }

    public function render(): ResponseInterface
    {
        return $this->withContent($this->renderer->render($this->template, $this->data));
    }

    public function with(string $key, mixed $value): ViewContract
    {
        $this->data[$key] = $value;
        return $this;
    }
}
