<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\Errors\ErrCodec;
use MultiversX\SmartContracts\Utils\Guard;
use MultiversX\SmartContracts\Typesystem\TypeSelectors;
use MultiversX\SmartContracts\Typesystem\TypedValue;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\Utils\Guards;

class BinaryCodec implements ICodec
{
    public BinaryCodecConstraints $constraints;
    public OptionValueBinaryCodec $optionCodec;
    public ListBinaryCodec $listCodec;
    public ArrayVecBinaryCodec $arrayCodec;
    public PrimitiveBinaryCodec $primitiveCodec;
    public StructBinaryCodec $structCodec;
    public TupleBinaryCodec $tupleCodec;
    public EnumBinaryCodec $enumCodec;
    public ExplicitEnumBinaryCodec $explicitEnumCodec;
    public ManagedDecimalCodec $managedDecimalCodec;
    public ManagedDecimalSignedCodec $managedDecimalSignedCodec;

    public function __construct(?array $constraints = null)
    {
        $this->constraints = new BinaryCodecConstraints($constraints);
        $this->optionCodec = new OptionValueBinaryCodec($this);
        $this->listCodec = new ListBinaryCodec($this);
        $this->arrayCodec = new ArrayVecBinaryCodec($this);
        $this->primitiveCodec = new PrimitiveBinaryCodec($this);
        $this->structCodec = new StructBinaryCodec($this);
        $this->tupleCodec = new TupleBinaryCodec($this);
        $this->enumCodec = new EnumBinaryCodec($this);
        $this->explicitEnumCodec = new ExplicitEnumBinaryCodec();
        $this->managedDecimalCodec = new ManagedDecimalCodec($this);
        $this->managedDecimalSignedCodec = new ManagedDecimalSignedCodec($this);
    }

    public function decodeTopLevel(string $buffer, Type $type): TypedValue
    {
        $this->constraints->checkBufferLength($buffer);

        return TypeSelectors::onTypeSelect($type, [
            'onOption' => fn() => $this->optionCodec->decodeTopLevel($buffer, $type->getFirstTypeParameter()),
            'onList' => fn() => $this->listCodec->decodeTopLevel($buffer, $type),
            'onArray' => fn() => $this->arrayCodec->decodeTopLevel($buffer, $type),
            'onPrimitive' => fn() => $this->primitiveCodec->decodeTopLevel($buffer, $type),
            'onStruct' => fn() => $this->structCodec->decodeTopLevel($buffer, $type),
            'onTuple' => fn() => $this->tupleCodec->decodeTopLevel($buffer, $type),
            'onEnum' => fn() => $this->enumCodec->decodeTopLevel($buffer, $type),
            'onExplicitEnum' => fn() => $this->explicitEnumCodec->decodeTopLevel($buffer, $type),
            'onManagedDecimal' => fn() => $this->managedDecimalCodec->decodeTopLevel($buffer, $type),
            'onManagedDecimalSigned' => fn() => $this->managedDecimalSignedCodec->decodeTopLevel($buffer, $type),
            'onOther' => fn() => throw new ErrCodec("Unknown type for top level decoding: {$type->getName()}")
        ]);
    }

    public function decodeNested(string $buffer, Type $type): array
    {
        $this->constraints->checkBufferLength($buffer);

        return TypeSelectors::onTypeSelect($type, [
            'onOption' => fn() => $this->optionCodec->decodeNested($buffer, $type->getFirstTypeParameter()),
            'onList' => fn() => $this->listCodec->decodeNested($buffer, $type),
            'onArray' => fn() => $this->arrayCodec->decodeNested($buffer, $type),
            'onPrimitive' => fn() => $this->primitiveCodec->decodeNested($buffer, $type),
            'onStruct' => fn() => $this->structCodec->decodeNested($buffer, $type),
            'onTuple' => fn() => $this->tupleCodec->decodeNested($buffer, $type),
            'onEnum' => fn() => $this->enumCodec->decodeNested($buffer, $type),
            'onExplicitEnum' => fn() => $this->explicitEnumCodec->decodeNested($buffer, $type),
            'onManagedDecimal' => fn() => $this->managedDecimalCodec->decodeNested($buffer, $type),
            'onManagedDecimalSigned' => fn() => $this->managedDecimalSignedCodec->decodeNested($buffer, $type),
            'onOther' => fn() => throw new ErrCodec("Unknown type for nested decoding: {$type->getName()}")
        ]);
    }

    public function encodeNested(TypedValue $typedValue): string
    {
        Guards::guardTrue($typedValue->getType()->getCardinality()->isSingular(), "singular cardinality, thus encodable type");

        return TypeSelectors::onTypedValueSelect($typedValue, [
            'onPrimitive' => fn() => $this->primitiveCodec->encodeNested($typedValue),
            'onOption' => fn() => $this->optionCodec->encodeNested($typedValue),
            'onList' => fn() => $this->listCodec->encodeNested($typedValue),
            'onArray' => fn() => $this->arrayCodec->encodeNested($typedValue),
            'onStruct' => fn() => $this->structCodec->encodeNested($typedValue),
            'onTuple' => fn() => $this->tupleCodec->encodeNested($typedValue),
            'onEnum' => fn() => $this->enumCodec->encodeNested($typedValue),
            'onExplicitEnum' => fn() => $this->explicitEnumCodec->encodeNested($typedValue),
            'onManagedDecimal' => fn() => $this->managedDecimalCodec->encodeNested($typedValue),
            'onManagedDecimalSigned' => fn() => $this->managedDecimalSignedCodec->encodeNested($typedValue),
            'onOther' => fn() => throw new ErrCodec("Unknown type for nested encoding: {$typedValue->getType()->getName()}")
        ]);
    }

    public function encodeTopLevel(TypedValue $typedValue): string
    {
        Guards::guardTrue($typedValue->getType()->getCardinality()->isSingular(), "singular cardinality, thus encodable type");

        return TypeSelectors::onTypedValueSelect($typedValue, [
            'onPrimitive' => fn() => $this->primitiveCodec->encodeTopLevel($typedValue),
            'onOption' => fn() => $this->optionCodec->encodeTopLevel($typedValue),
            'onList' => fn() => $this->listCodec->encodeTopLevel($typedValue),
            'onArray' => fn() => $this->arrayCodec->encodeTopLevel($typedValue),
            'onStruct' => fn() => $this->structCodec->encodeTopLevel($typedValue),
            'onTuple' => fn() => $this->tupleCodec->encodeTopLevel($typedValue),
            'onEnum' => fn() => $this->enumCodec->encodeTopLevel($typedValue),
            'onExplicitEnum' => fn() => $this->explicitEnumCodec->encodeTopLevel($typedValue),
            'onManagedDecimal' => fn() => $this->managedDecimalCodec->encodeTopLevel($typedValue),
            'onManagedDecimalSigned' => fn() => $this->managedDecimalSignedCodec->encodeTopLevel($typedValue),
            'onOther' => fn() => throw new ErrCodec("Unknown type for top level encoding: {$typedValue->getType()->getName()}")
        ]);
    }
}
