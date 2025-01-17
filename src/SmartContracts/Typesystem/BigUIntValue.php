<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use Brick\Math\BigInteger;

class BigUIntValue extends NumericalValue
{
    public const ClassName = 'BigUIntValue';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new BigUIntType(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
