<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\ExplicitEnumValue;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumType;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumVariantDefinition;

class ExplicitEnumBinaryCodec implements ICodec
{
    private StringBinaryCodec $stringCodec;

    public function __construct()
    {
        $this->stringCodec = new StringBinaryCodec();
    }

    public function decodeTopLevel(string $buffer, ExplicitEnumType $type): ExplicitEnumValue
    {
        $stringValue = $this->stringCodec->decodeTopLevel($buffer);
        return new ExplicitEnumValue($type, new ExplicitEnumVariantDefinition($stringValue->valueOf()));
    }

    public function decodeNested(string $buffer, ExplicitEnumType $type): array
    {
        [$value, $length] = $this->stringCodec->decodeNested($buffer);
        $enumValue = new ExplicitEnumValue($type, new ExplicitEnumVariantDefinition($value->valueOf()));

        return [$enumValue, $length];
    }

    public function encodeNested(ExplicitEnumValue $enumValue): string
    {
        return $this->stringCodec->encodeNested(new StringValue($enumValue->valueOf()['name']));
    }

    public function encodeTopLevel(ExplicitEnumValue $enumValue): string
    {
        return $this->stringCodec->encodeTopLevel(new StringValue($enumValue->valueOf()['name']));
    }
}
