<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\I32Type;
use Brick\Math\BigInteger;

class I32Value extends NumericalValue
{
    public const ClassName = 'I32Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new I32Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
