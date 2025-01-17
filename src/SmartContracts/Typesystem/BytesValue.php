<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\BytesType;

class BytesValue extends PrimitiveValue
{
    public const ClassName = 'BytesValue';
    private string $value;

    public function __construct(string $value)
    {
        parent::__construct(new BytesType());
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Creates a BytesValue from a utf-8 string.
     */
    public static function fromUTF8(string $value): BytesValue
    {
        return new BytesValue($value);
    }

    /**
     * Creates a BytesValue from a hex-encoded string.
     */
    public static function fromHex(string $value): BytesValue
    {
        return new BytesValue(hex2bin($value));
    }

    public function getLength(): int
    {
        return strlen($this->value);
    }

    /**
     * Returns whether two objects have the same value.
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof BytesValue)) {
            return false;
        }

        if ($this->getLength() != $other->getLength()) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function valueOf(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
