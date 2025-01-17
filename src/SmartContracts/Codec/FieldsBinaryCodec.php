<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Field;
use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;

class FieldsBinaryCodec implements ICodec
{
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    /**
     * @param string $buffer
     * @param FieldDefinition[] $fieldDefinitions
     * @return array{0: Field[], 1: int}
     */
    public function decodeNested(string $buffer, array $fieldDefinitions): array
    {
        $fields = [];
        $totalLength = 0;

        foreach ($fieldDefinitions as $fieldDefinition) {
            [$decoded, $decodedLength] = $this->binaryCodec->decodeNested($buffer, $fieldDefinition->type);
            $buffer = substr($buffer, $decodedLength);
            $totalLength += $decodedLength;

            $field = new Field($decoded, $fieldDefinition->name);
            $fields[] = $field;
        }

        return [$fields, $totalLength];
    }

    /**
     * @param Field[] $fields
     */
    public function encodeNested(array $fields): string
    {
        $buffers = [];

        foreach ($fields as $field) {
            $fieldBuffer = $this->binaryCodec->encodeNested($field->value);
            $buffers[] = $fieldBuffer;
        }

        return implode('', $buffers);
    }
}
