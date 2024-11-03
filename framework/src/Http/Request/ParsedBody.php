<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http\Request;

class ParsedBody
{
    /**
     * @return ?array<string, mixed>
     */
    public function get(): ?array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'POST') {
            if (str_contains($contentType, 'application/json')) {
                $contents = file_get_contents('php://input');
                $data = $contents ? $contents : '';
                return json_decode($data, true, flags: JSON_THROW_ON_ERROR);
            }
            return $_POST;
        }

        return null;
    }
}
