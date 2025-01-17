<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Fields;
use MultiversX\Utils\Guards;

class EnumVariantDefinition
{
    private const SimpleEnumMaxDiscriminant = 256;

    /**
     * @param string $name
     * @param int $discriminant
     * @param FieldDefinition[] $fieldsDefinitions
     */
    public function __construct(
        public readonly string $name,
        public readonly int $discriminant,
        private readonly array $fieldsDefinitions = []
    ) {
        Guards::guardTrue(
            $discriminant < self::SimpleEnumMaxDiscriminant,
            sprintf('discriminant for simple enum should be less than %d', self::SimpleEnumMaxDiscriminant)
        );
    }

    /**
     * @param array{name: string, discriminant: int, fields?: array<mixed>} $json
     */
    public static function fromJSON(array $json): self
    {
        $definitions = array_map(
            fn($definition) => FieldDefinition::fromJSON($definition),
            $json['fields'] ?? []
        );
        return new self($json['name'], $json['discriminant'], $definitions);
    }

    /**
     * @return FieldDefinition[]
     */
    public function getFieldsDefinitions(): array
    {
        return $this->fieldsDefinitions;
    }

    public function getFieldDefinition(string $name): ?FieldDefinition
    {
        foreach ($this->fieldsDefinitions as $definition) {
            if ($definition->name === $name) {
                return $definition;
            }
        }
        return null;
    }

    /**
     * @return string[]
     */
    public function getNamesOfDependencies(): array
    {
        return Fields::getNamesOfTypeDependencies($this->fieldsDefinitions);
    }
}
