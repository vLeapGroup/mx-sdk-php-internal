<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use Brick\Math\BigInteger;

class U32Value extends NumericalValue
{
    public const ClassName = 'U32Value';

    public function __construct(int|BigInteger $value)
    {
        parent::__construct(new U32Type(), $value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }
}
