<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\StringType;

class StringValue extends PrimitiveValue
{
    public const ClassName = 'StringValue';
    private string $value;

    public function __construct(string $value)
    {
        parent::__construct(new StringType());
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Creates a StringValue from a utf-8 string.
     */
    public static function fromUTF8(string $value): StringValue
    {
        return new StringValue($value);
    }

    /**
     * Creates a StringValue from a hex-encoded string.
     */
    public static function fromHex(string $value): StringValue
    {
        $decodedValue = hex2bin($value);
        return new StringValue($decodedValue);
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
        if (!($other instanceof StringValue)) {
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
