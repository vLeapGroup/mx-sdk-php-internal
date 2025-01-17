<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\BytesValue;

class StringBinaryCodec implements ICodec
{
    private BytesBinaryCodec $bytesBinaryCodec;

    public function __construct()
    {
        $this->bytesBinaryCodec = new BytesBinaryCodec();
    }

    /**
     * @param string $buffer
     * @return array [StringValue, int]
     */
    public function decodeNested(string $buffer): array
    {
        [$decoded, $length] = $this->bytesBinaryCodec->decodeNested($buffer);
        $decodedAsString = new StringValue($decoded->valueOf());
        return [$decodedAsString, $length];
    }

    public function decodeTopLevel(string $buffer): StringValue
    {
        return new StringValue($buffer);
    }

    public function encodeNested(StringValue $value): string
    {
        $valueAsBytes = BytesValue::fromUTF8($value->valueOf());
        return $this->bytesBinaryCodec->encodeNested($valueAsBytes);
    }

    public function encodeTopLevel(StringValue $value): string
    {
        return $value->valueOf();
    }
}
