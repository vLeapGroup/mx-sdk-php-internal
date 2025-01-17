<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\TypeCardinality;

class CountedVariadicType extends Type
{
    private const ClassName = "VariadicType";

    public function __construct(Type $typeParameter)
    {
        parent::__construct("Variadic", [$typeParameter], TypeCardinality::variable());
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
