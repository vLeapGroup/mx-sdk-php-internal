<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class BooleanType extends PrimitiveType
{
    public const ClassName = 'BooleanType';

    public function __construct()
    {
        parent::__construct('bool');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
