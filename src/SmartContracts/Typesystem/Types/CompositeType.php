<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class CompositeType extends Type
{
    public const ClassName = "CompositeType";

    public function __construct(Type ...$typeParameters)
    {
        parent::__construct("Composite", $typeParameters, TypeCardinality::variable(count($typeParameters)));
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
