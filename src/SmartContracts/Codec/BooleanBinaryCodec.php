<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\Errors\ErrInvalidArgument;
use MultiversX\SmartContracts\Typesystem\BooleanValue;

/**
 * Encodes and decodes "BooleanValue" objects.
 */
class BooleanBinaryCodec
{
    private const TRUE = 0x01;
    private const FALSE = 0x00;

    public function decodeNested(string $buffer): array
    {
        // We don't check the size of the buffer, we just read the first byte
        $byte = ord($buffer[0]);
        return [new BooleanValue($byte == self::TRUE), 1];
    }

    public function decodeTopLevel(string $buffer): BooleanValue
    {
        if (strlen($buffer) > 1) {
            throw new ErrInvalidArgument("buffer should be of size <= 1");
        }

        $firstByte = strlen($buffer) ? ord($buffer[0]) : 0;
        return new BooleanValue($firstByte == self::TRUE);
    }

    public function encodeNested(BooleanValue $primitive): string
    {
        if ($primitive->isTrue()) {
            return chr(self::TRUE);
        }

        return chr(self::FALSE);
    }

    public function encodeTopLevel(BooleanValue $primitive): string
    {
        if ($primitive->isTrue()) {
            return chr(self::TRUE);
        }

        return '';
    }
}
