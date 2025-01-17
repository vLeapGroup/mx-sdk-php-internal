<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\ArrayVec;
use MultiversX\SmartContracts\Typesystem\Types\ArrayVecType;

class ArrayVecBinaryCodec implements ICodec
{
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    public function decodeNested(string $buffer, ArrayVecType $type): array
    {
        $arrayLength = $type->length;
        $typeParameter = $type->getFirstTypeParameter();
        $result = [];
        $totalLength = 0;

        for ($i = 0; $i < $arrayLength; $i++) {
            [$decoded, $decodedLength] = $this->binaryCodec->decodeNested($buffer, $typeParameter);
            $result[] = $decoded;
            $totalLength += $decodedLength;
            $buffer = substr($buffer, $decodedLength);
        }

        return [new ArrayVec($type, $result), $totalLength];
    }

    public function decodeTopLevel(string $buffer, ArrayVecType $type): ArrayVec
    {
        [$result, $_] = $this->decodeNested($buffer, $type);
        return $result;
    }

    public function encodeNested(ArrayVec $array): string
    {
        $itemsBuffers = [];

        foreach ($array->getItems() as $item) {
            $itemBuffer = $this->binaryCodec->encodeNested($item);
            $itemsBuffers[] = $itemBuffer;
        }

        return implode('', $itemsBuffers);
    }

    public function encodeTopLevel(ArrayVec $array): string
    {
        return $this->encodeNested($array);
    }
}
