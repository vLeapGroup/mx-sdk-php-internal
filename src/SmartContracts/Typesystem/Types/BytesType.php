<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class BytesType extends PrimitiveType
{
    public const ClassName = 'BytesType';

    public function __construct()
    {
        parent::__construct('bytes');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
