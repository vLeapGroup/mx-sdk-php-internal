<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\H256Type;

class H256Value extends PrimitiveValue
{
    public const ClassName = 'H256Value';
    private string $value;

    public function __construct(string $value)
    {
        parent::__construct(new H256Type());
        $this->value = $value;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Returns whether two objects have the same value.
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof H256Value)) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function valueOf(): string
    {
        return $this->value;
    }
}
