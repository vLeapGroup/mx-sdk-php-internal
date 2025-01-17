<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Exceptions\TypingSystemError;
use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;

class Field
{
    public function __construct(
        public readonly TypedValue $value,
        public readonly string $name = ''
    ) {
    }

    public function checkTyping(FieldDefinition $expectedDefinition): void
    {
        $actualType = $this->value->getType();

        if (!$actualType->equals($expectedDefinition->type)) {
            throw new TypingSystemError(
                sprintf(
                    'check type of field "%s"; expected: %s, actual: %s',
                    $expectedDefinition->name,
                    $expectedDefinition->type,
                    $actualType
                )
            );
        }

        if ($this->name !== $expectedDefinition->name) {
            throw new TypingSystemError(
                sprintf('check name of field "%s"', $expectedDefinition->name)
            );
        }
    }

    public function equals(Field $other): bool
    {
        return $this->name === $other->name && $this->value->equals($other->value);
    }
}
