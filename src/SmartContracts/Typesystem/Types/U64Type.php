<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class U64Type extends NumericalType
{
    public const ClassName = 'U64Type';

    public function __construct()
    {
        parent::__construct('u64', 8, false);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
