<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class ListType extends Type
{
    public const ClassName = "ListType";

    public function __construct(Type $typeParameter)
    {
        parent::__construct("List", [$typeParameter]);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
