<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class H256Type extends PrimitiveType
{
    public const ClassName = 'H256Type';

    public function __construct()
    {
        parent::__construct('H256');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
