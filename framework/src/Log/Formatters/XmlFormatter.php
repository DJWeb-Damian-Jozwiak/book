<?php

declare(strict_types=1);

namespace DJWeb\Framework\Log\Formatters;

use DJWeb\Framework\Log\Message;
use SimpleXMLElement;

final readonly class XmlFormatter extends Formatter
{
    /**
     * @param Message $message
     *
     * @return array<string, mixed>
     */
    public function toArray(Message $message): array
    {
        return [
            'datetime' => date('Y-m-d H:i:s'),
            'level' => $message->level->name,
            'message' => $message->message,
            'context' => $message->context->all(),
            'metadata' => $message->metadata?->toArray(),
        ];
    }

    public function format(Message $message): string
    {
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><log/>');
        $this->arrayToXml($this->toArray($message), $xml);
        $data = $xml->asXML();
        return $data ? $data : '';
    }

    /**
     * @param array<string, mixed> $data
     * @param SimpleXMLElement $xml
     *
     * @return void
     */
    private function arrayToXml(array $data, SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $subnode = $xml->addChild($key);
                $this->arrayToXml($value, $subnode);

            } else {
                $xml->addChild($key, htmlspecialchars((string) $value));

            }

        }
    }

}
