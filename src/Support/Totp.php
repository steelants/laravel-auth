<?php

namespace SteelAnts\LaravelAuth\Support;

class Totp
{
    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function otpauthUrl(string $label, string $issuer, string $secret, int $digits = 6, int $period = 30): string
    {
        $encodedLabel = rawurlencode($issuer . ':' . $label);
        $encodedIssuer = rawurlencode($issuer);

        return sprintf(
            'otpauth://totp/%s?secret=%s&issuer=%s&digits=%d&period=%d',
            $encodedLabel,
            $secret,
            $encodedIssuer,
            $digits,
            $period
        );
    }

    public static function verify(string $secret, string $code, int $window = 1, int $period = 30, int $digits = 6): bool
    {
        $now = time();

        for ($i = -$window; $i <= $window; $i++) {
            $timestamp = $now + ($i * $period);
            if (self::generateCode($secret, $timestamp, $period, $digits) === $code) {
                return true;
            }
        }

        return false;
    }

    public static function generateCode(string $secret, ?int $timestamp = null, int $period = 30, int $digits = 6): string
    {
        $timeSlice = (int) floor(($timestamp ?? time()) / $period);
        $secretKey = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord(substr($hash, -1)) & 0x0F;
        $value = (ord($hash[$offset]) & 0x7F) << 24 |
            (ord($hash[$offset + 1]) & 0xFF) << 16 |
            (ord($hash[$offset + 2]) & 0xFF) << 8 |
            (ord($hash[$offset + 3]) & 0xFF);

        $mod = 10 ** $digits;

        return str_pad((string) ($value % $mod), $digits, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $binary = '';

        foreach (str_split($data) as $char) {
            $binary .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $chunks = str_split($binary, 5);
        $secret = '';

        foreach ($chunks as $chunk) {
            $secret .= $alphabet[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))];
        }

        return $secret;
    }

    private static function base32Decode(string $secret): string
    {
        $alphabet = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $binary = '';

        foreach (str_split(strtoupper($secret)) as $char) {
            if (!isset($alphabet[$char])) {
                continue;
            }
            $binary .= str_pad(decbin($alphabet[$char]), 5, '0', STR_PAD_LEFT);
        }

        $bytes = str_split($binary, 8);
        $decoded = '';

        foreach ($bytes as $byte) {
            if (strlen($byte) === 8) {
                $decoded .= chr(bindec($byte));
            }
        }

        return $decoded;
    }
}
