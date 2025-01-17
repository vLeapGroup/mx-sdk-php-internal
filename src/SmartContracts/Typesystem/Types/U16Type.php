<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class U16Type extends NumericalType
{
    public const ClassName = 'U16Type';

    public function __construct()
    {
        parent::__construct('u16', 2, false);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
