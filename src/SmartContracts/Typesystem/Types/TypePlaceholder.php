<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class TypePlaceholder extends Type
{
    protected const ClassName = "TypePlaceholder";

    public function __construct()
    {
        parent::__construct("...");
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
