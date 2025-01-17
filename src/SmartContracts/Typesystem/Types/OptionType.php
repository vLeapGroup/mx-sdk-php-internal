<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class OptionType extends Type
{
    public const ClassName = "OptionType";

    public function __construct(Type $typeParameter)
    {
        parent::__construct("Option", [$typeParameter]);
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
