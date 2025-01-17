<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use InvalidArgumentException;

class ArrayVecType extends Type
{
    public const ClassName = 'ArrayVecType';
    public readonly int $length;

    public function __construct(int $length, Type $typeParameter)
    {
        parent::__construct('Array', [$typeParameter]);

        if ($length <= 0) {
            throw new InvalidArgumentException('array length > 0');
        }

        $this->length = $length;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
