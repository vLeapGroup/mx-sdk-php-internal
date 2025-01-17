<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class I16Type extends NumericalType
{
    public const ClassName = 'I16Type';

    public function __construct()
    {
        parent::__construct('i16', 2, true);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
