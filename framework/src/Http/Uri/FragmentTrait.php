<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Uri;

use Psr\Http\Message\UriInterface;

trait FragmentTrait
{
    private string $fragment = '';
    public function getFragment(): string
    {
        return $this->fragment;
    }
    public function withFragment(string $fragment): UriInterface
    {
        return $this->clone($this, 'fragment', $fragment);
    }
}
