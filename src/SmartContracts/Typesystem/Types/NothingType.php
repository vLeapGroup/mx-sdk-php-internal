<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class NothingType extends PrimitiveType
{
    public const ClassName = 'NothingType';

    public function __construct()
    {
        parent::__construct('nothing');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
