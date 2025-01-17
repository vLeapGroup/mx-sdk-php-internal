<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\NothingValue;

class NothingCodec implements ICodec
{
    /**
     * @return array [NothingValue, int]
     */
    public function decodeNested(): array
    {
        return [new NothingValue(), 0];
    }

    public function decodeTopLevel(): NothingValue
    {
        return new NothingValue();
    }

    public function encodeNested(): string
    {
        return '';
    }

    public function encodeTopLevel(): string
    {
        return '';
    }
}
