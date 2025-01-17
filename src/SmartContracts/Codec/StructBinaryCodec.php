<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\Struct;

class StructBinaryCodec implements ICodec
{
    private FieldsBinaryCodec $fieldsCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->fieldsCodec = new FieldsBinaryCodec($binaryCodec);
    }

    public function decodeTopLevel(string $buffer, StructType $type): Struct
    {
        [$decoded] = $this->decodeNested($buffer, $type);
        return $decoded;
    }

    public function decodeNested(string $buffer, StructType $type): array
    {
        $fieldDefinitions = $type->getFieldsDefinitions();
        [$fields, $offset] = $this->fieldsCodec->decodeNested($buffer, $fieldDefinitions);
        $struct = new Struct($type, $fields);
        return [$struct, $offset];
    }

    public function encodeNested(Struct $struct): string
    {
        $fields = $struct->getFields();
        return $this->fieldsCodec->encodeNested($fields);
    }

    public function encodeTopLevel(Struct $struct): string
    {
        return $this->encodeNested($struct);
    }
}
