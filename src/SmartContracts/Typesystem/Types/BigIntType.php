<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class BigIntType extends NumericalType
{
    public const ClassName = 'BigIntType';

    public function __construct()
    {
        parent::__construct('Bigint', 0, true);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
