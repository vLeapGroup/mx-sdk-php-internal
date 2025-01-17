<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\U8Type;
use Brick\Math\BigInteger;

class U8Value extends NumericalValue
{
    public const ClassName = 'U8Value';

    public function __construct(string|int|BigInteger $value)
    {
        parent::__construct(new U8Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
