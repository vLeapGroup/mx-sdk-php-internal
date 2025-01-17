<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

abstract class NumericalType extends PrimitiveType
{
    public const ClassName = 'NumericalType';

    protected int $sizeInBytes;
    protected bool $withSign;

    protected function __construct(string $name, int $sizeInBytes, bool $withSign)
    {
        parent::__construct($name);
        $this->sizeInBytes = $sizeInBytes;
        $this->withSign = $withSign;
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function hasFixedSize(): bool
    {
        return (bool)$this->sizeInBytes;
    }

    public function hasArbitrarySize(): bool
    {
        return !$this->hasFixedSize();
    }

    public function getSizeInBytes(): int
    {
        return $this->sizeInBytes;
    }

    public function getWithSign(): bool
    {
        return $this->withSign;
    }
}
