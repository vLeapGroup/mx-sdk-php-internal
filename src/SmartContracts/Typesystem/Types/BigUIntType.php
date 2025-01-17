<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class BigUIntType extends NumericalType
{
    public const ClassName = 'BigUIntType';

    public function __construct()
    {
        parent::__construct('BigUint', 0, false);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
