<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\Errors\ErrCodec;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;

/**
 * Encodes and decodes "OptionValue" objects
 */
class OptionValueBinaryCodec implements ICodec
{
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    public function decodeNested(string $buffer, Type $type): array
    {
        if (ord($buffer[0]) === 0x00) {
            return [OptionValue::newMissingTyped($type), 1];
        }

        if (ord($buffer[0]) !== 0x01) {
            throw new ErrCodec("invalid buffer for optional value");
        }

        [$decoded, $decodedLength] = $this->binaryCodec->decodeNested(substr($buffer, 1), $type);
        return [OptionValue::newProvided($decoded), $decodedLength + 1];
    }

    public function decodeTopLevel(string $buffer, Type $type): OptionValue
    {
        if (strlen($buffer) === 0) {
            return new OptionValue(new OptionType($type));
        }

        if (ord($buffer[0]) !== 0x01) {
            throw new ErrCodec("invalid buffer for optional value");
        }

        [$decoded, $_decodedLength] = $this->binaryCodec->decodeNested(substr($buffer, 1), $type);
        return new OptionValue(new OptionType($type), $decoded);
    }

    public function encodeNested(OptionValue $optionValue): string
    {
        if ($optionValue->isSet()) {
            return chr(1) . $this->binaryCodec->encodeNested($optionValue->getTypedValue());
        }

        return chr(0);
    }

    public function encodeTopLevel(OptionValue $optionValue): string
    {
        if ($optionValue->isSet()) {
            return chr(1) . $this->binaryCodec->encodeNested($optionValue->getTypedValue());
        }

        return '';
    }
}
