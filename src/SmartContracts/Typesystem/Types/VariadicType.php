<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\TypeCardinality;

class VariadicType extends Type
{
    public const ClassName = "VariadicType";

    public function __construct(
        Type $typeParameter,
        public bool $isCounted = false
    ) {
        parent::__construct("Variadic", [$typeParameter], TypeCardinality::variable());
        $this->isCounted = $isCounted;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
