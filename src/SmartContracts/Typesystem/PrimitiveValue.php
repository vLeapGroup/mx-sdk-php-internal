<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\Type;

abstract class PrimitiveValue extends TypedValue
{
    public const ClassName = "PrimitiveValue";

    public function __construct(Type $type)
    {
        parent::__construct($type);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
