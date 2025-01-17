<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\Address;
use MultiversX\SmartContracts\Typesystem\Types\AddressType;

/**
 * An address fed to or fetched from a Smart Contract contract, as an immutable abstraction.
 */
class AddressValue extends PrimitiveValue
{
    public const ClassName = 'AddressValue';
    private Address $value;

    public function __construct(Address $value)
    {
        parent::__construct(new AddressType());
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Returns whether two objects have the same value.
     *
     * @param AddressValue $other another AddressValue
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof AddressValue)) {
            return false;
        }
        return $this->value->equals($other->value);
    }

    public function valueOf(): Address
    {
        return $this->value;
    }
}
