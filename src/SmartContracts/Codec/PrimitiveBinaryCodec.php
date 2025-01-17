<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\TypeSelectors;
use MultiversX\SmartContracts\Typesystem\Types\PrimitiveType;
use MultiversX\SmartContracts\Typesystem\PrimitiveValue;

class PrimitiveBinaryCodec implements ICodec
{
    public BinaryCodec $binaryCodec;
    public BooleanBinaryCodec $booleanCodec;
    public NumericalBinaryCodec $numericalCodec;
    public AddressBinaryCodec $addressCodec;
    public H256BinaryCodec $h256Codec;
    public BytesBinaryCodec $bytesCodec;
    public StringBinaryCodec $stringCodec;
    public TokenIdentifierCodec $tokenIdentifierCodec;
    public CodeMetadataCodec $codeMetadataCodec;
    public NothingCodec $nothingCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
        $this->booleanCodec = new BooleanBinaryCodec();
        $this->numericalCodec = new NumericalBinaryCodec();
        $this->addressCodec = new AddressBinaryCodec();
        $this->h256Codec = new H256BinaryCodec();
        $this->bytesCodec = new BytesBinaryCodec();
        $this->stringCodec = new StringBinaryCodec();
        $this->tokenIdentifierCodec = new TokenIdentifierCodec();
        $this->codeMetadataCodec = new CodeMetadataCodec();
        $this->nothingCodec = new NothingCodec();
    }

    public function decodeNested(string $buffer, PrimitiveType $type): array
    {
        return TypeSelectors::onPrimitiveTypeSelect($type, [
            'onBoolean' => fn() => $this->booleanCodec->decodeNested($buffer),
            'onNumerical' => fn() => $this->numericalCodec->decodeNested($buffer, $type),
            'onAddress' => fn() => $this->addressCodec->decodeNested($buffer),
            'onBytes' => fn() => $this->bytesCodec->decodeNested($buffer),
            'onString' => fn() => $this->stringCodec->decodeNested($buffer),
            'onH256' => fn() => $this->h256Codec->decodeNested($buffer),
            'onTokenIndetifier' => fn() => $this->tokenIdentifierCodec->decodeNested($buffer),
            'onCodeMetadata' => fn() => $this->codeMetadataCodec->decodeNested($buffer),
            'onNothing' => fn() => $this->nothingCodec->decodeNested(),
            'onOther' => fn() => throw new \InvalidArgumentException("Unknown primitive type: {$type->getName()}")
        ]);
    }

    public function decodeTopLevel(string $buffer, PrimitiveType $type): PrimitiveValue
    {
        return TypeSelectors::onPrimitiveTypeSelect($type, [
            'onBoolean' => fn() => $this->booleanCodec->decodeTopLevel($buffer),
            'onNumerical' => fn() => $this->numericalCodec->decodeTopLevel($buffer, $type),
            'onAddress' => fn() => $this->addressCodec->decodeTopLevel($buffer),
            'onBytes' => fn() => $this->bytesCodec->decodeTopLevel($buffer),
            'onString' => fn() => $this->stringCodec->decodeTopLevel($buffer),
            'onH256' => fn() => $this->h256Codec->decodeTopLevel($buffer),
            'onTokenIndetifier' => fn() => $this->tokenIdentifierCodec->decodeTopLevel($buffer),
            'onCodeMetadata' => fn() => $this->codeMetadataCodec->decodeTopLevel($buffer),
            'onNothing' => fn() => $this->nothingCodec->decodeTopLevel(),
            'onOther' => fn() => throw new \InvalidArgumentException("Unknown primitive type: {$type->getName()}")
        ]);
    }

    public function encodeNested(PrimitiveValue $value): string
    {
        return TypeSelectors::onPrimitiveValueSelect($value, [
            'onBoolean' => fn() => $this->booleanCodec->encodeNested($value),
            'onNumerical' => fn() => $this->numericalCodec->encodeNested($value),
            'onAddress' => fn() => $this->addressCodec->encodeNested($value),
            'onBytes' => fn() => $this->bytesCodec->encodeNested($value),
            'onString' => fn() => $this->stringCodec->encodeNested($value),
            'onH256' => fn() => $this->h256Codec->encodeNested($value),
            'onTypeIdentifier' => fn() => $this->tokenIdentifierCodec->encodeNested($value),
            'onCodeMetadata' => fn() => $this->codeMetadataCodec->encodeNested($value),
            'onNothing' => fn() => $this->nothingCodec->encodeNested(),
            'onOther' => fn() => throw new \InvalidArgumentException("Unknown primitive value type: {$value->getType()->getName()}")
        ]);
    }

    public function encodeTopLevel(PrimitiveValue $value): string
    {
        return TypeSelectors::onPrimitiveValueSelect($value, [
            'onBoolean' => fn() => $this->booleanCodec->encodeTopLevel($value),
            'onNumerical' => fn() => $this->numericalCodec->encodeTopLevel($value),
            'onAddress' => fn() => $this->addressCodec->encodeTopLevel($value),
            'onBytes' => fn() => $this->bytesCodec->encodeTopLevel($value),
            'onString' => fn() => $this->stringCodec->encodeTopLevel($value),
            'onH256' => fn() => $this->h256Codec->encodeTopLevel($value),
            'onTypeIdentifier' => fn() => $this->tokenIdentifierCodec->encodeTopLevel($value),
            'onCodeMetadata' => fn() => $this->codeMetadataCodec->encodeTopLevel($value),
            'onNothing' => fn() => $this->nothingCodec->encodeTopLevel(),
            'onOther' => fn() => throw new \InvalidArgumentException("Unknown primitive value type: {$value->getType()->getName()}")
        ]);
    }
}
