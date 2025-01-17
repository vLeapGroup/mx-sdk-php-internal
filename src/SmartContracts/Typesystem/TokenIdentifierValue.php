<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;

class TokenIdentifierValue extends PrimitiveValue
{
    private const EGLD_TOKEN_IDENTIFIER = 'EGLD';
    public const ClassName = 'TokenIdentifierValue';
    private string $value;

    public function __construct(string $value)
    {
        parent::__construct(new TokenIdentifierType());
        $this->value = $value;
    }

    public static function egld(): TokenIdentifierValue
    {
        return new TokenIdentifierValue(self::EGLD_TOKEN_IDENTIFIER);
    }

    public static function esdtTokenIdentifier(string $identifier): TokenIdentifierValue
    {
        return new TokenIdentifierValue($identifier);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function getLength(): int
    {
        return strlen($this->value);
    }

    /**
     * Returns whether two objects have the same value.
     */
    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof TokenIdentifierValue)) {
            return false;
        }

        if ($this->getLength() != $other->getLength()) {
            return false;
        }

        return $this->value === $other->value;
    }

    public function valueOf(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
