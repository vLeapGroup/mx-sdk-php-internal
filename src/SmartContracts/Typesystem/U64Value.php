<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use Brick\Math\BigInteger;

class U64Value extends NumericalValue
{
    public const ClassName = 'U64Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new U64Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
