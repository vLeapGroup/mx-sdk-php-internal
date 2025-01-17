<?php

namespace MultiversX\SmartContracts\Typesystem;

use Brick\Math\BigDecimal;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalType;

class ManagedDecimalValue extends TypedValue
{
    public const ClassName = 'ManagedDecimalValue';
    private BigDecimal $value;
    private int $scale;
    private bool $variable;

    public function __construct(BigDecimal $value, int $scale, bool $isVariable = false)
    {
        parent::__construct(new ManagedDecimalType($isVariable ? 'usize' : $scale));
        $this->value = $value;
        $this->scale = $scale;
        $this->variable = $isVariable;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function getScale(): int
    {
        return $this->scale;
    }

    public function getPrecision(): int
    {
        return strlen(str_replace('.', '', $this->value->toScale($this->scale)->__toString()));
    }

    /**
     * Returns whether two objects have the same value.
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof ManagedDecimalValue)) {
            return false;
        }

        if ($this->getPrecision() !== $other->getPrecision()) {
            return false;
        }

        return $this->value->isEqualTo($other->value);
    }

    public function valueOf(): BigDecimal
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value->toScale($this->scale)->__toString();
    }

    public function isVariable(): bool
    {
        return $this->variable;
    }
}
