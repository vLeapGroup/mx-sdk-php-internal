<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class I32Type extends NumericalType
{
    public const ClassName = 'I32Type';

    public function __construct()
    {
        parent::__construct('i32', 4, true);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
