<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\I64Type;
use Brick\Math\BigInteger;

class I64Value extends NumericalValue
{
    public const ClassName = 'I64Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new I64Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
