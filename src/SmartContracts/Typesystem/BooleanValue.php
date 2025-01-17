<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\BooleanType;

/**
 * A boolean value fed to or fetched from a Smart Contract contract, as an immutable abstraction.
 */
class BooleanValue extends PrimitiveValue
{
    public const ClassName = 'BooleanValue';
    private bool $value;

    public function __construct(bool $value)
    {
        parent::__construct(new BooleanType());
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Returns whether two objects have the same value.
     *
     * @param BooleanValue $other another BooleanValue
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof BooleanValue)) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function isTrue(): bool
    {
        return $this->value === true;
    }

    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    public function valueOf(): bool
    {
        return $this->value;
    }
}
