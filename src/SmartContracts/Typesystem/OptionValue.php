<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\NullType;
use MultiversX\SmartContracts\Typesystem\Types\Type;

class OptionValue extends TypedValue
{
    public const ClassName = "OptionValue";

    public function __construct(
        OptionType $type,
        private readonly ?TypedValue $value = null
    ) {
        parent::__construct($type);
        // TODO: assert value is of type type.getFirstTypeParameter()
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * Creates an OptionValue, as a missing option argument.
     */
    public static function newMissing(): self
    {
        $type = new OptionType(new NullType());
        return new self($type);
    }

    public static function newMissingTyped(Type $type): self
    {
        return new self(new OptionType($type));
    }

    /**
     * Creates an OptionValue, as a provided option argument.
     */
    public static function newProvided(TypedValue $typedValue): self
    {
        $type = new OptionType($typedValue->getType());
        return new self($type, $typedValue);
    }

    public function isSet(): bool
    {
        return $this->value !== null;
    }

    public function getTypedValue(): TypedValue
    {
        if ($this->value === null) {
            throw new \RuntimeException("Value is not set");
        }
        return $this->value;
    }

    public function valueOf(): mixed
    {
        return $this->value?->valueOf() ?? null;
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof OptionValue)) {
            return false;
        }

        return $this->value?->equals($other->value) ?? false;
    }
}
