<?php

namespace MultiversX\SmartContracts\Typesystem;

use Brick\Math\BigDecimal;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalSignedType;

class ManagedDecimalSignedValue extends TypedValue
{
    public const ClassName = 'ManagedDecimalSignedValue';

    /**
     * @param BigDecimal|string|int $value
     * @param int $scale
     * @param bool $isVariable
     */
    public function __construct(
        private readonly BigDecimal|string|int $value,
        private readonly int $scale,
        private readonly bool $isVariable = false
    ) {
        parent::__construct(
            new ManagedDecimalSignedType($isVariable ? 'usize' : $scale)
        );
        $this->value = BigDecimal::of($value);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function getPrecision(): int
    {
        return strlen(str_replace('.', '', $this->value->toScale($this->scale)));
    }

    public function getScale(): int
    {
        return $this->scale;
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof ManagedDecimalSignedValue)) {
            return false;
        }

        if ($this->getPrecision() !== $other->getPrecision()) {
            return false;
        }

        return BigDecimal::of($this->value)->isEqualTo($other->value);
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value->toScale($this->scale);
    }

    public function isVariable(): bool
    {
        return $this->isVariable;
    }
}
