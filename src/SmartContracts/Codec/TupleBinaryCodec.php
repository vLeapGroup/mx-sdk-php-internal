<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Types\TupleType;
use MultiversX\SmartContracts\Typesystem\Tuple;
use MultiversX\SmartContracts\Typesystem\Struct;

class TupleBinaryCodec
{
    private StructBinaryCodec $structCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->structCodec = new StructBinaryCodec($binaryCodec);
    }

    public function decodeTopLevel(string $buffer, TupleType $type): Struct
    {
        return $this->structCodec->decodeTopLevel($buffer, $type);
    }

    public function decodeNested(string $buffer, TupleType $type): array
    {
        return $this->structCodec->decodeNested($buffer, $type);
    }

    public function encodeNested(Tuple $struct): string
    {
        return $this->structCodec->encodeNested($struct);
    }

    public function encodeTopLevel(Struct $struct): string
    {
        return $this->structCodec->encodeTopLevel($struct);
    }
}
