<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\ListValue;

/**
 * Encodes and decodes "List" objects.
 */
class ListBinaryCodec implements ICodec
{
    private const SIZE_OF_U32 = 4;
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    public function decodeNested(string $buffer, Type $type): array
    {
        $typeParameter = $type->getFirstTypeParameter();
        $result = [];
        $numItems = unpack('N', $buffer)[1];
        $this->binaryCodec->constraints->checkListLength($numItems);

        $originalBuffer = $buffer;
        $offset = self::SIZE_OF_U32;

        $buffer = substr($originalBuffer, $offset);

        for ($i = 0; $i < $numItems; $i++) {
            [$decoded, $decodedLength] = $this->binaryCodec->decodeNested($buffer, $typeParameter);
            $result[] = $decoded;
            $offset += $decodedLength;
            $buffer = substr($originalBuffer, $offset);
        }

        return [new ListValue($type, $result), $offset];
    }

    public function decodeTopLevel(string $buffer, Type $type): ListValue
    {
        $typeParameter = $type->getFirstTypeParameter();
        $result = [];

        $originalBuffer = $buffer;
        $offset = 0;

        while (strlen($buffer) > 0) {
            [$decoded, $decodedLength] = $this->binaryCodec->decodeNested($buffer, $typeParameter);
            $result[] = $decoded;
            $offset += $decodedLength;
            $buffer = substr($originalBuffer, $offset);

            $this->binaryCodec->constraints->checkListLength(count($result));
        }

        return new ListValue($type, $result);
    }

    public function encodeNested(ListValue $list): string
    {
        $this->binaryCodec->constraints->checkListLength($list->getLength());

        $lengthBuffer = pack('N', $list->getLength());
        $itemsBuffers = [];

        foreach ($list->getItems() as $item) {
            $itemBuffer = $this->binaryCodec->encodeNested($item);
            $itemsBuffers[] = $itemBuffer;
        }

        return $lengthBuffer . implode('', $itemsBuffers);
    }

    public function encodeTopLevel(ListValue $list): string
    {
        $this->binaryCodec->constraints->checkListLength($list->getLength());

        $itemsBuffers = [];

        foreach ($list->getItems() as $item) {
            $itemBuffer = $this->binaryCodec->encodeNested($item);
            $itemsBuffers[] = $itemBuffer;
        }

        return implode('', $itemsBuffers);
    }
}
