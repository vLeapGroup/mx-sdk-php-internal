<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class I8Type extends NumericalType
{
    public const ClassName = 'I8Type';

    public function __construct()
    {
        parent::__construct('i8', 1, true);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
