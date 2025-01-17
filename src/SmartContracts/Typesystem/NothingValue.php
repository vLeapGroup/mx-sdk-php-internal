<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\NothingType;

class NothingValue extends PrimitiveValue
{
    public const ClassName = 'NothingValue';

    public function __construct()
    {
        parent::__construct(new NothingType());
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof NothingValue)) {
            return false;
        }
        return false;
    }

    public function valueOf(): array
    {
        return [];
    }
}
