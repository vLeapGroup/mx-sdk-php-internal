<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class I64Type extends NumericalType
{
    public const ClassName = 'I64Type';

    public function __construct()
    {
        parent::__construct('i64', 8, true);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
