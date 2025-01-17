<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class PrimitiveType extends Type
{
    public const ClassName = "PrimitiveType";

    public function __construct(string $name)
    {
        parent::__construct($name);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
