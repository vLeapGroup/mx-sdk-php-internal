<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

abstract class CustomType extends Type
{
    protected const ClassName = "CustomType";

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
