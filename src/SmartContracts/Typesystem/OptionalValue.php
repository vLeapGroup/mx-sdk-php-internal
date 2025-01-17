<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\NullType;
use MultiversX\Utils\Guards;

class OptionalValue extends TypedValue
{
    public const ClassName = 'OptionalValue';
    private ?TypedValue $value;

    public function __construct(OptionalType $type, ?TypedValue $value = null)
    {
        parent::__construct($type);
        // TODO: assert value is of type type.getFirstTypeParameter()
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Creates an OptionalValue, as not provided (missing).
     */
    public static function newMissing(): OptionalValue
    {
        $type = new OptionalType(new NullType());
        return new OptionalValue($type);
    }

    public function isSet(): bool
    {
        return $this->value !== null;
    }

    public function getTypedValue(): TypedValue
    {
        Guards::guardValueIsSet('value', $this->value);
        return $this->value;
    }

    public function valueOf(): mixed
    {
        return $this->value ? $this->value->valueOf() : null;
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof OptionalValue)) {
            return false;
        }

        return $this->value?->equals($other->value) ?? false;
    }
}
