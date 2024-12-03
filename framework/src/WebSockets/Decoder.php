<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

class Decoder
{
    /**
     * Decodes a WebSocket frame according to RFC 6455 section 5.2
     *
     * @link https://tools.ietf.org/html/rfc6455#section-5.2
     */
    public static function decode(string $data): Frame
    {
        $bytes = unpack('C*', $data);

        // The first byte contains the FIN bit, RSV1-3 bits, and the opcode (RFC 6455, section 5.2)
        $firstByte = array_shift($bytes);
        $fin = (bool) ($firstByte & 128);
        $opcode = Opcode::from($firstByte & 15);

        // The second byte contains the mask bit and the payload length (RFC 6455, section 5.2)
        $secondByte = array_shift($bytes);
        $mask = (bool) ($secondByte & 128);
        $payloadLength = $secondByte & 127;

        // Extended payload length (RFC 6455, section 5.2)
        if ($payloadLength === 126) {
            $payloadLength = ($bytes[0] << 8) | $bytes[1];
            $bytes = array_slice($bytes, 2);
        } elseif ($payloadLength === 127) {
            $payloadLength = 0;
            for ($i = 0; $i < 8; $i++) {
                $payloadLength = ($payloadLength << 8) | $bytes[$i];
            }
            $bytes = array_slice($bytes, 8);
        }

        // Masking key
        $maskingKey = null;
        if ($mask) {
            $maskingKey = array_slice($bytes, 0, 4);
            $bytes = array_slice($bytes, 4);
        }

        // Payload
        $payload = pack('C*', ...$bytes);
        if ($mask) {
            $payload = self::unmaskPayload($payload, $maskingKey);
        }

        return new Frame($fin, $opcode, $mask, $payloadLength, $maskingKey ? pack('C*', ...$maskingKey) : null, $payload);
    }

    private static function unmaskPayload(string $payload, array $maskingKey): string
    {
        $unmaskedPayload = '';
        $payloadLength = strlen($payload);
        for ($i = 0; $i < $payloadLength; $i++) {
            $unmaskedPayload .= $payload[$i] ^ chr($maskingKey[$i % 4]);
        }
        return $unmaskedPayload;
    }
}
