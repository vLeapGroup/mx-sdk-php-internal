<?php

namespace MultiversX\SmartContracts\Typesystem;

use Brick\Math\BigInteger;
use MultiversX\SmartContracts\Typesystem\Types\BigIntType;

class BigIntValue extends NumericalValue
{
    public const ClassName = 'BigIntValue';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new BigIntType(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
