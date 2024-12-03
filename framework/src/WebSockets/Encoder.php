<?php

declare(strict_types=1);

namespace DJWeb\Framework\WebSockets;

class Encoder
{
    /**
     * Encodes a WebSocket frame according to RFC 6455 section 5.2
     *
     * @link https://tools.ietf.org/html/rfc6455#section-5.2
     */
    public static function encode(Frame $frame): string
    {
        $frameHead = [];

        // Set the first byte: FIN bit and opcode (RFC 6455, section 5.2)
        $frameHead[0] = ($frame->fin ? 128 : 0) | $frame->opcode->value;

        // Set the second byte: Mask bit and payload length (RFC 6455, section 5.2)
        $payloadLength = strlen($frame->payload);
        if ($payloadLength <= 125) {
            $frameHead[1] = ($frame->mask ? 128 : 0) | $payloadLength;
        } elseif ($payloadLength <= 65535) {
            $frameHead[1] = ($frame->mask ? 128 : 0) | 126;
            $frameHead[2] = ($payloadLength >> 8) & 255;
            $frameHead[3] = $payloadLength & 255;
        } else {
            $frameHead[1] = ($frame->mask ? 128 : 0) | 127;
            for ($i = 0; $i < 8; $i++) {
                $frameHead[2 + $i] = $payloadLength >> (7 - $i) * 8 & 255;
            }
        }

        $encodedFrame = pack('C*', ...$frameHead);

        if ($frame->mask) {
            $encodedFrame .= $frame->maskingKey;
            $encodedFrame .= self::maskPayload($frame->payload, $frame->maskingKey);
        } else {
            $encodedFrame .= $frame->payload;
        }

        return $encodedFrame;
    }

    private static function maskPayload(string $payload, string $maskingKey): string
    {
        $maskedPayload = '';
        for ($i = 0; $i < strlen($payload); $i++) {
            $maskedPayload .= $payload[$i] ^ $maskingKey[$i % 4];
        }
        return $maskedPayload;
    }
}
