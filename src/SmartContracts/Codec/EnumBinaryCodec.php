<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Types\EnumType;
use MultiversX\SmartContracts\Typesystem\Types\U8Type;
use MultiversX\SmartContracts\Typesystem\U8Value;
use MultiversX\SmartContracts\Typesystem\EnumValue;
use MultiversX\SmartContracts\Typesystem\Field;

class EnumBinaryCodec implements ICodec
{
    private BinaryCodec $binaryCodec;
    private FieldsBinaryCodec $fieldsCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
        $this->fieldsCodec = new FieldsBinaryCodec($binaryCodec);
    }

    public function decodeTopLevel(string $buffer, EnumType $type): EnumValue
    {
        // This handles enums without fields, with discriminant = 0, as well.
        [$enumValue] = $this->decodeNested($buffer, $type);
        return $enumValue;
    }

    public function decodeNested(string $buffer, EnumType $type): array
    {
        [$discriminant, $lengthOfDiscriminant] = $this->readDiscriminant($buffer);
        $buffer = substr($buffer, $lengthOfDiscriminant);

        $variant = $type->getVariantByDiscriminant($discriminant);
        $fieldDefinitions = $variant->getFieldsDefinitions();

        [$fields, $lengthOfFields] = $this->fieldsCodec->decodeNested($buffer, $fieldDefinitions);
        $enumValue = new EnumValue($type, $variant, $fields);

        return [$enumValue, $lengthOfDiscriminant + $lengthOfFields];
    }

    private function readDiscriminant(string $buffer): array
    {
        [$value, $length] = $this->binaryCodec->decodeNested($buffer, new U8Type());
        $discriminant = $value->valueOf();

        return [$discriminant, $length];
    }

    public function encodeNested(EnumValue $enumValue): string
    {
        $discriminant = new U8Value($enumValue->discriminant);
        $discriminantBuffer = $this->binaryCodec->encodeNested($discriminant);

        $fields = $enumValue->getFields();
        $fieldsBuffer = $this->fieldsCodec->encodeNested($fields);

        return $discriminantBuffer . $fieldsBuffer;
    }

    public function encodeTopLevel(EnumValue $enumValue): string
    {
        $fields = $enumValue->getFields();
        $hasFields = count($fields) > 0;
        $fieldsBuffer = $this->fieldsCodec->encodeNested($fields);

        $discriminant = new U8Value($enumValue->discriminant);
        $discriminantBuffer = $hasFields
            ? $this->binaryCodec->encodeNested($discriminant)
            : $this->binaryCodec->encodeTopLevel($discriminant);

        return $discriminantBuffer . $fieldsBuffer;
    }
}
