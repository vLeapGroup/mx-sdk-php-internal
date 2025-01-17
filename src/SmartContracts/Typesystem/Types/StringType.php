<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class StringType extends PrimitiveType
{
    public const ClassName = 'StringType';

    public function __construct()
    {
        parent::__construct('utf-8 string');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
