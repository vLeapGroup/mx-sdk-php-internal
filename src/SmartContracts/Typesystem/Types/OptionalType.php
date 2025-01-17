<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\TypeCardinality;

/**
 * An optional is an algebraic type. It holds zero or one values.
 */
class OptionalType extends Type
{
    public const ClassName = 'OptionalType';

    public function __construct(Type $typeParameter)
    {
        parent::__construct('Optional', [$typeParameter], TypeCardinality::variable(1));
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function isAssignableFrom(Type $type): bool
    {
        if (!$type->hasExactClass(self::ClassName)) {
            return false;
        }

        $invariantTypeParameters = $this->getFirstTypeParameter()->equals($type->getFirstTypeParameter());
        $fakeCovarianceToNull = $type->getFirstTypeParameter()->hasExactClass(NullType::ClassName);
        return $invariantTypeParameters || $fakeCovarianceToNull;
    }
}
