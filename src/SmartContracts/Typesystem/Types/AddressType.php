<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class AddressType extends PrimitiveType
{
    public const ClassName = 'AddressType';

    public function __construct()
    {
        parent::__construct('Address');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
