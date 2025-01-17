<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class U32Type extends NumericalType
{
    public const ClassName = 'U32Type';

    public function __construct()
    {
        parent::__construct('u32', 4, false);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
