<?php

namespace App\Services;

use App\Models\User;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class TwoFactorAuthService
{
    /**
     * Characters allowed in Base32 string (RFC 4648).
     */
    protected const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a random 16-character Base32 secret key.
     */
    public static function generateSecretKey(int $length = 16): string
    {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;
        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }

        return $secret;
    }

    /**
     * Generate emergency recovery backup codes.
     *
     * @return array<int, string>
     */
    public static function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $part1 = strtoupper(bin2hex(random_bytes(2)));
            $part2 = strtoupper(bin2hex(random_bytes(2)));
            $codes[] = "{$part1}-{$part2}";
        }

        return $codes;
    }

    /**
     * Generate standard OTPAuth URL for Google Authenticator / Authy.
     */
    public static function getOtpAuthUrl(User $user, string $secret): string
    {
        $issuer = rawurlencode(config('app.name', 'RestaurantPOS'));
        $label = rawurlencode($user->email);

        return "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Render QR code as SVG Data URI for immediate browser display.
     */
    public static function renderQrSvg(string $otpAuthUrl): string
    {
        /** @var mixed $options */
        $options = new QROptions([
            'outputBase64' => false,
        ]);

        return (new QRCode($options))->render($otpAuthUrl);
    }

    /**
     * Calculate 6-digit TOTP code for a given timestamp and secret.
     */
    public static function calculateTotp(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        $timeSlice = floor($timestamp / 30);

        $binarySecret = self::base32Decode($secret);

        // Pack timeSlice into 8-byte big-endian binary
        $timePack = pack('N*', 0).pack('N*', $timeSlice);

        // Hash with HMAC-SHA1
        $hmac = hash_hmac('sha1', $timePack, $binarySecret, true);

        // Dynamic truncation (RFC 4226)
        $offset = ord(substr($hmac, -1)) & 0x0F;
        $hashPart = substr($hmac, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verify the 6-digit TOTP code with time drift window tolerance (±1 slice = 30 seconds).
     */
    public static function verify(string $secret, string $code): bool
    {
        $currentTime = time();

        // Check current, previous (-30s), and next (+30s) windows to account for clock drift
        for ($i = -1; $i <= 1; $i++) {
            $expectedCode = self::calculateTotp($secret, $currentTime + ($i * 30));
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Decode a Base32 encoded string into raw binary.
     */
    protected static function base32Decode(string $base32): string
    {
        $base32 = strtoupper(trim($base32));
        $buffer = 0;
        $bitsLeft = 0;
        $output = '';

        for ($i = 0, $len = strlen($base32); $i < $len; $i++) {
            $char = $base32[$i];
            $val = strpos(self::BASE32_CHARS, $char);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $output .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $output;
    }
}
