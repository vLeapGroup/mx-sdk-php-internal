<?php

namespace MultiversX\SmartContracts\Codec\Utils;

use Brick\Math\BigInteger;

class BufferUtil
{
    /**
     * Checks if the most significant bit of the first byte is zero.
     */
    public static function isMsbZero(string $buffer): bool
    {
        if (empty($buffer)) {
            return true;
        }

        return (ord($buffer[0]) & 0x80) === 0;
    }

    /**
     * Checks if the most significant bit of the first byte is one.
     */
    public static function isMsbOne(string $buffer): bool
    {
        return !self::isMsbZero($buffer);
    }

    /**
     * Flips all bits in the buffer in place.
     */
    public static function flipBufferBitsInPlace(string &$buffer): void
    {
        $length = strlen($buffer);
        for ($i = 0; $i < $length; $i++) {
            $buffer[$i] = chr(~ord($buffer[$i]) & 0xFF);
        }
    }

    /**
     * Prepends a byte to the buffer.
     */
    public static function prependByteToBuffer(string $buffer, string $byte): string
    {
        return $byte . $buffer;
    }

    /**
     * Converts a buffer to a BigInteger.
     */
    public static function bufferToBigInt(string $buffer): BigInteger
    {
        if (empty($buffer)) {
            return BigInteger::zero();
        }

        return BigInteger::fromBase(bin2hex($buffer), 16);
    }

    /**
     * Converts a BigInteger to a buffer.
     */
    public static function bigIntToBuffer(BigInteger $value): string
    {
        if ($value->isZero()) {
            return '';
        }

        $hex = $value->toBase(16);
        if (strlen($hex) % 2 !== 0) {
            $hex = '0' . $hex;
        }

        return hex2bin($hex);
    }
}
