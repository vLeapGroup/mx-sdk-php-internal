<?php

namespace MultiversX;

use Exception;
use function BitWasp\Bech32\convertBits;
use function BitWasp\Bech32\decode;
use function BitWasp\Bech32\encode;
use InvalidArgumentException;
use MultiversX\Interfaces\IAddress;
use Throwable;

class Address implements IAddress
{
    const DEFAULT_HRP = 'erd';
    const CONTRACT_HEX_PUBKEY_PREFIX = '0000000000000000';
    const PUBKEY_LENGTH = 32;

    private function __construct(
        private string $valueHex,
        public readonly string $hrp = self::DEFAULT_HRP
    ) {
    }

    public static function newFromHex(string $value, string $hrp = self::DEFAULT_HRP): Address
    {
        if (! self::isValidHex($value)) {
            throw new InvalidArgumentException('invalid hex value');
        }

        return new Address(
            $value ?: throw new InvalidArgumentException('hex value is required'),
            $hrp
        );
    }

    public static function newFromBech32(string $address): Address
    {
        try {
            [$hrp, $decoded] = decode(strtolower($address));

            $res = convertBits($decoded, count($decoded), 5, 8, false);
            $pieces = array_map(fn ($bits) => dechex($bits), $res);
            $hex = array_reduce($pieces, fn ($carry, $hex) => $carry . str_pad($hex, 2, "0", STR_PAD_LEFT));

            return new Address($hex, $hrp);
        } catch (Throwable $e) {
            throw new Exception("cannot create address from {$address}: {$e->getMessage()}");
        }
    }

    public static function newFromBase64(string $value, string $hrp = self::DEFAULT_HRP): Address
    {
        return new Address(bin2hex(base64_decode($value)), $hrp);
    }

    public static function zero(string $hrp = self::DEFAULT_HRP): Address
    {
        return new Address(str_repeat('0', 64), $hrp);
    }

    public function hex(): string
    {
        return $this->valueHex;
    }

    public function bech32(): string
    {
        $bin = hex2bin($this->valueHex);
        $bits = array_values(unpack('C*', $bin));

        return encode($this->hrp, convertBits($bits, count($bits), 8, 5));
    }

    public function getPublicKey(): string
    {
        return hex2bin($this->valueHex);
    }

    public function getHrp(): string
    {
        return $this->hrp;
    }

    public function isSmartContract(): bool
    {
        return str_starts_with($this->valueHex, self::CONTRACT_HEX_PUBKEY_PREFIX);
    }

    public function isEmpty(): bool
    {
        return empty($this->valueHex);
    }

    public function equals(?Address $other): bool
    {
        return $other !== null
            ? $this->valueHex === $other->hex()
            : false;
    }

    public function __toString(): string
    {
        return $this->bech32();
    }

    private static function isValidHex(string $value): bool
    {
        return ctype_xdigit($value) && strlen($value) === 64;
    }

    public static function isValid(string $address): bool
    {
        try {
            $decoded = decode($address);
            [$hrp, $data] = $decoded;
            $pubkey = convertBits($data, count($data), 5, 8, false);

            return count($pubkey) === self::PUBKEY_LENGTH;
        } catch (Throwable) {
            return false;
        }
    }
}
