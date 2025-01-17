<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\Errors\MissingFieldOnEnumError;
use MultiversX\SmartContracts\Typesystem\Types\EnumType;

class EnumValue extends TypedValue
{
    public const ClassName = 'EnumValue';
    public readonly string $name;
    public readonly int $discriminant;
    /** @var Field[] */
    private readonly array $fields;
    /** @var array<string, Field> */
    private readonly array $fieldsByName;

    /**
     * @param EnumType $type
     * @param EnumVariantDefinition $variant
     * @param Field[] $fields
     */
    public function __construct(
        EnumType $type,
        EnumVariantDefinition $variant,
        array $fields = []
    ) {
        parent::__construct($type);
        $this->name = $variant->name;
        $this->discriminant = $variant->discriminant;
        $this->fields = $fields;
        $this->fieldsByName = array_combine(
            array_map(fn($field) => $field->name, $fields),
            $fields
        );

        $definitions = $variant->getFieldsDefinitions();
        Fields::checkTyping($this->fields, $definitions);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Utility (named constructor) to create a simple (i.e. without fields) enum value.
     */
    public static function fromName(EnumType $type, string $name): self
    {
        $variant = $type->getVariantByName($name);
        return new self($type, $variant, []);
    }

    /**
     * Utility (named constructor) to create a simple (i.e. without fields) enum value.
     */
    public static function fromDiscriminant(EnumType $type, int $discriminant): self
    {
        $variant = $type->getVariantByDiscriminant($discriminant);
        return new self($type, $variant, []);
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof EnumValue)) {
            return false;
        }

        if (!$this->getType()->equals($other->getType())) {
            return false;
        }

        $selfFields = $this->getFields();
        $otherFields = $other->getFields();

        $nameIsSame = $this->name === $other->name;
        $discriminantIsSame = $this->discriminant === $other->discriminant;
        $fieldsAreSame = Fields::equals($selfFields, $otherFields);

        return $nameIsSame && $discriminantIsSame && $fieldsAreSame;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getFieldValue(string $name): mixed
    {
        if (isset($this->fieldsByName[$name])) {
            return $this->fieldsByName[$name]->value->valueOf();
        }

        throw new MissingFieldOnEnumError($name, $this->getType()->getName());
    }

    public function valueOf(): array
    {
        $result = [
            'name' => $this->name,
            'fields' => []
        ];

        foreach ($this->fields as $index => $field) {
            $result['fields'][$index] = $field->value->valueOf();
        }

        return $result;
    }
}
