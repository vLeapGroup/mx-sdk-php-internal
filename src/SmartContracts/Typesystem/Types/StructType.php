<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;
use MultiversX\SmartContracts\Typesystem\Fields;

class StructType extends CustomType
{
    public const ClassName = 'StructType';

    /**
     * @param string $name
     * @param FieldDefinition[] $fieldsDefinitions
     */
    public function __construct(
        string $name,
        private readonly array $fieldsDefinitions = []
    ) {
        parent::__construct($name);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * @param array{name: string, fields: array<mixed>} $json
     */
    public static function fromJSON(array $json): self
    {
        $definitions = array_map(
            fn($definition) => FieldDefinition::fromJSON($definition),
            $json['fields'] ?? []
        );
        return new self($json['name'], $definitions);
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
