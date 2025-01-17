<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class TypeCardinality
{
    private static int $MaxCardinality = 4096;

    private int $lowerBound;
    private ?int $upperBound;

    private function __construct(int $lowerBound, ?int $upperBound = null)
    {
        $this->lowerBound = $lowerBound;
        $this->upperBound = $upperBound;
    }

    public static function fixed(int $value): self
    {
        return new self($value, $value);
    }

    public static function variable(?int $value = null): self
    {
        return new self(0, $value);
    }

    public function isSingular(): bool
    {
        return $this->lowerBound === 1 && $this->upperBound === 1;
    }

    public function isSingularOrNone(): bool
    {
        return $this->lowerBound === 0 && $this->upperBound === 1;
    }

    public function isComposite(): bool
    {
        return $this->upperBound !== 1;
    }

    public function isFixed(): bool
    {
        return $this->lowerBound === $this->upperBound;
    }

    public function getLowerBound(): int
    {
        return $this->lowerBound;
    }

    public function getUpperBound(): int
    {
        return $this->upperBound ?? self::$MaxCardinality;
    }
}
