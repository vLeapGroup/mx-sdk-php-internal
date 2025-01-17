<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class U8Type extends NumericalType
{
    public const ClassName = 'U8Type';

    public function __construct()
    {
        parent::__construct('u8', 1, false);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
