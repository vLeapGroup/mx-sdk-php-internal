<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class TokenIdentifierType extends PrimitiveType
{
    public const ClassName = 'TokenIdentifierType';

    public function __construct()
    {
        parent::__construct('TokenIdentifier');
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
