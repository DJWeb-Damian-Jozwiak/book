<?php

declare(strict_types=1);

namespace DJWeb\Framework\Http;

class JsonResponse extends Response
{
    public function __construct(
        public mixed $data,
        int $status = 200,
        array $headers = [],
        public int $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR,
        string $version = '1.1'
    )
    {
        $body = $this->encodeData($data);

        $headers['Content-Type'] = 'application/json; charset=utf-8';

        parent::__construct(
            headers: $headers,
            body: new Stream()->withContent($body),
            version: $version,
            status: $status
        );
    }

     private function encodeData(mixed $data): string
    {
        return json_encode($data, $this->flags);
     }

}
