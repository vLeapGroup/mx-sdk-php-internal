<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;

class TokenIdentifierCodec implements ICodec
{
    private BytesBinaryCodec $bytesCodec;

    public function __construct()
    {
        $this->bytesCodec = new BytesBinaryCodec();
    }

    /**
     * @param string $buffer
     * @return array [TokenIdentifierValue, int]
     */
    public function decodeNested(string $buffer): array
    {
        [$bytesValue, $length] = $this->bytesCodec->decodeNested($buffer);
        return [new TokenIdentifierValue($bytesValue->valueOf()), $length];
    }

    public function decodeTopLevel(string $buffer): TokenIdentifierValue
    {
        $bytesValue = $this->bytesCodec->decodeTopLevel($buffer);
        return new TokenIdentifierValue($bytesValue->valueOf());
    }

    public function encodeNested(TokenIdentifierValue $tokenIdentifier): string
    {
        $bytesValue = BytesValue::fromUTF8($tokenIdentifier->valueOf());
        return $this->bytesCodec->encodeNested($bytesValue);
    }

    public function encodeTopLevel(TokenIdentifierValue $tokenIdentifier): string
    {
        return $tokenIdentifier->valueOf();
    }
}
