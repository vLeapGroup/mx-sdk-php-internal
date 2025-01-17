<?php

namespace MultiversX\SmartContracts\Abi;

class TypeFormula
{
    /**
     * @param string $name
     * @param TypeFormula[] $typeParameters
     * @param mixed|null $metadata
     */
    public function __construct(
        public readonly string $name,
        public readonly array $typeParameters = [],
        public readonly mixed $metadata = null
    ) {
    }

    public function __toString(): string
    {
        $hasTypeParameters = count($this->typeParameters) > 0;
        $typeParameters = $hasTypeParameters
            ? '<' . implode(', ', array_map(fn($tp) => (string)$tp, $this->typeParameters)) . '>'
            : '';
        $baseName = "{$this->name}{$typeParameters}";

        return $this->metadata !== null ? "{$baseName}*{$this->metadata}*" : $baseName;
    }
}
