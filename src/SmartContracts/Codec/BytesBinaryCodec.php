<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\BytesValue;

/**
 * Encodes and decodes "BytesValue" objects.
 */
class BytesBinaryCodec implements ICodec
{
    private const SIZE_OF_U32 = 4;

    /**
     * @param string $buffer
     * @return array [BytesValue, int]
     */
    public function decodeNested(string $buffer): array
    {
        $length = unpack('N', $buffer)[1];
        $payload = substr($buffer, self::SIZE_OF_U32, $length);
        $result = new BytesValue($payload);
        return [$result, self::SIZE_OF_U32 + $length];
    }

    public function decodeTopLevel(string $buffer): BytesValue
    {
        return new BytesValue($buffer);
    }

    public function encodeNested(BytesValue $bytes): string
    {
        $lengthBuffer = pack('N', $bytes->getLength());
        return $lengthBuffer . $bytes->valueOf();
    }

    public function encodeTopLevel(BytesValue $bytes): string
    {
        return $bytes->valueOf();
    }
}
