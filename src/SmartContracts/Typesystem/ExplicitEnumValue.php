<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumType;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumVariantDefinition;

class ExplicitEnumValue extends TypedValue
{
    public const ClassName = 'ExplicitEnumValue';

    public function __construct(
        ExplicitEnumType $type,
        private readonly ExplicitEnumVariantDefinition $variant
    ) {
        parent::__construct($type);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Utility (named constructor) to create a simple (i.e. without fields) enum value.
     */
    public static function fromName(ExplicitEnumType $type, string $name): self
    {
        $variant = $type->getVariantByName($name);
        return new self($type, $variant);
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof ExplicitEnumValue)) {
            return false;
        }

        if (!$this->getType()->equals($other->getType())) {
            return false;
        }

        return $this->variant->name === $other->variant->name;
    }

    public function valueOf(): array
    {
        return ['name' => $this->variant->name];
    }
}
