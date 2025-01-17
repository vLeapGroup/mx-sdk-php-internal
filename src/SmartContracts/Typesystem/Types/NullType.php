<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class NullType extends Type
{
    protected const ClassName = "NullType";

    public function __construct()
    {
        parent::__construct("?");
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
