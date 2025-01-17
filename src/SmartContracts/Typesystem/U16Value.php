<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\U16Type;
use Brick\Math\BigInteger;

class U16Value extends NumericalValue
{
    public const ClassName = 'U16Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new U16Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
