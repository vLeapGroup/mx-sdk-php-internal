<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\EnumVariantDefinition;
use MultiversX\SmartContracts\Utils\Guard;
use MultiversX\Utils\Guards;

class EnumType extends CustomType
{
    public const ClassName = 'EnumType';

    /**
     * @param string $name
     * @param EnumVariantDefinition[] $variants
     */
    public function __construct(
        string $name,
        public readonly array $variants = []
    ) {
        parent::__construct($name);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * @param array{name: string, variants: array<mixed>} $json
     */
    public static function fromJSON(array $json): self
    {
        $rawVariants = self::assignMissingDiscriminants($json['variants'] ?? []);
        $variants = array_map(
            fn($variant) => EnumVariantDefinition::fromJSON($variant),
            $rawVariants
        );
        return new self($json['name'], $variants);
    }

    /**
     * For some enums (e.g. some "explicit-enum" types), the discriminants are missing.
     * @param array<mixed> $variants
     * @return array<mixed>
     */
    private static function assignMissingDiscriminants(array $variants): array
    {
        $allDiscriminantsAreMissing = array_reduce(
            $variants,
            fn($carry, $variant) => $carry && !isset($variant['discriminant']),
            true
        );

        if (!$allDiscriminantsAreMissing) {
            // We only assign discriminants if all of them are missing.
            return $variants;
        }

        return array_map(
            fn($variant, $index) => array_merge($variant, ['discriminant' => $index]),
            $variants,
            array_keys($variants)
        );
    }

    public function getVariantByDiscriminant(int $discriminant): EnumVariantDefinition
    {
        foreach ($this->variants as $variant) {
            if ($variant->discriminant === $discriminant) {
                return $variant;
            }
        }

        Guards::guardTrue(
            false,
            sprintf('variant by discriminant (%d)', $discriminant)
        );
    }

    public function getVariantByName(string $name): EnumVariantDefinition
    {
        foreach ($this->variants as $variant) {
            if ($variant->name === $name) {
                return $variant;
            }
        }

        Guards::guardTrue(
            false,
            sprintf('variant by name (%s)', $name)
        );
    }

    /**
     * @return string[]
     */
    public function getNamesOfDependencies(): array
    {
        $dependencies = [];

        foreach ($this->variants as $variant) {
            $dependencies = array_merge($dependencies, $variant->getNamesOfDependencies());
        }

        return array_values(array_unique($dependencies));
    }
}
