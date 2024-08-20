<?php

namespace JWT;

use CustomeException\InvalidFormatException;
use CustomeException\InvalidSignatureException;
use CustomeException\TokenExpiredException;

trait JWTcodec
{

    protected const KEY = "06abd17d0feb5ca4365f02c356ce1243a9d02a0c92e9dfa4d0df55aee6b725a8";

    protected function encode(array $payload): string
    {
        $hedaer = json_encode([
            "typ" => "JWT",
            "alg" => "HS256"
        ]);

        $hedaer = $this->base64urlEncode($hedaer);

        $payload = json_encode($payload);
        $payload = $this->base64urlEncode($payload);

        $signature = hash_hmac("sha256", $hedaer . "." . $payload, self::KEY, true);

        $signature = $this->base64urlEncode($signature);

        return $hedaer . "." . $payload . "." . $signature;
    }

    protected function decode(string $token): array
    {

        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", $token, $matches) !== 1) {

            throw new InvalidFormatException;
        }
        $signature = hash_hmac("sha256", $matches["header"] . "." . $matches["payload"], self::KEY, true);

        $signature_from_token = $this->base64urlDecode($matches["signature"]);

        if (! hash_equals($signature, $signature_from_token)) {
            throw new InvalidSignatureException;
        }

        $payload = json_decode($this->base64urlDecode($matches["payload"]), true);

        if ($payload["exp"] < time()) {
            throw new TokenExpiredException;
        }

        return $payload;
    }


    private function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+", "/", "="],
            ["-", "_", ""],
            base64_encode($text)
        );
    }

    private function base64urlDecode(string $text): string
    {
        return base64_decode(
            str_replace(
                ["-", "_"],
                ["+", "/"],
                $text
            )
        );
    }
}
