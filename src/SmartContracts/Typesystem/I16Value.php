<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\I16Type;
use Brick\Math\BigInteger;

class I16Value extends NumericalValue
{
    public const ClassName = 'I16Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new I16Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
