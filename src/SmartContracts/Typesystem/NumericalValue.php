<?php

namespace MultiversX\SmartContracts\Typesystem;

use Brick\Math\BigInteger;
use MultiversX\Errors\ErrInvalidArgument;
use MultiversX\SmartContracts\Typesystem\Types\NumericalType;

/**
 * A numerical value fed to or fetched from a Smart Contract contract, as a strongly-typed, immutable abstraction.
 */
class NumericalValue extends PrimitiveValue
{
    public const ClassName = 'NumericalValue';
    public BigInteger $value;
    public ?int $sizeInBytes;
    public bool $withSign;

    public function __construct(NumericalType $type, int|BigInteger $value)
    {
        parent::__construct($type);

        $this->value = BigInteger::of($value);
        $this->sizeInBytes = $type->getSizeInBytes();
        $this->withSign = $type->getWithSign();

        if (!$this->withSign && $this->value->isNegative()) {
            throw new ErrInvalidArgument("negative, but type is unsigned: {$this->value}");
        }
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Returns whether two objects have the same value.
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof NumericalValue)) {
            return false;
        }

        return $this->value->isEqualTo($other->value);
    }

    public function valueOf(): int|BigInteger
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
