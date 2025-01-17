<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\I8Type;
use Brick\Math\BigInteger;

class I8Value extends NumericalValue
{
    public const ClassName = 'I8Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new I8Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
