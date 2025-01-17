<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumVariantDefinition;
use MultiversX\Utils\Guards;

class ExplicitEnumType extends CustomType
{
    public const ClassName = 'ExplicitEnumType';

    /**
     * @param string $name
     * @param ExplicitEnumVariantDefinition[] $variants
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
        $variants = array_map(
            fn($variant) => ExplicitEnumVariantDefinition::fromJSON($variant),
            $json['variants']
        );
        return new self($json['name'], $variants);
    }

    public function getVariantByName(string $name): ExplicitEnumVariantDefinition
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
}
