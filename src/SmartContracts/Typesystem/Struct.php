<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Exceptions\MissingFieldOnStructError;
use MultiversX\SmartContracts\Typesystem\Types\StructType;

class Struct extends TypedValue
{
    public const ClassName = 'Struct';

    /**
     * @param StructType $type
     * @param Field[] $fields
     */
    public function __construct(
        StructType $type,
        private readonly array $fields
    ) {
        parent::__construct($type);
        $this->checkTyping();
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    private function checkTyping(): void
    {
        /** @var StructType $type */
        $type = $this->getType();
        $definitions = $type->getFieldsDefinitions();
        Fields::checkTyping($this->fields, $definitions);
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
        foreach ($this->fields as $field) {
            if ($field->name === $name) {
                return $field->value->valueOf();
            }
        }

        throw new MissingFieldOnStructError($name, $this->getType()->getName());
    }

    public function valueOf(): array
    {
        $result = [];

        foreach ($this->fields as $field) {
            $result[$field->name] = $field->value->valueOf();
        }

        return $result;
    }

    public function equals(TypedValue $other): bool
    {
        if (!$other instanceof Struct) {
            return false;
        }

        if (!$this->getType()->equals($other->getType())) {
            return false;
        }

        $selfFields = $this->getFields();
        $otherFields = $other->getFields();

        return Fields::equals($selfFields, $otherFields);
    }
}
