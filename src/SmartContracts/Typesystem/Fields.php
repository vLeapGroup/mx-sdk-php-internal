<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\Errors\TypingSystemError;


class Fields
{
    /**
     * @param Field[] $fields
     * @param FieldDefinition[] $definitions
     */
    public static function checkTyping(array $fields, array $definitions): void
    {
        if (count($fields) !== count($definitions)) {
            throw new TypingSystemError('fields length vs. field definitions length');
        }

        foreach ($fields as $i => $field) {
            $field->checkTyping($definitions[$i]);
        }
    }

    /**
     * @param Field[] $actual
     * @param Field[] $expected
     */
    public static function equals(array $actual, array $expected): bool
    {
        if (count($actual) !== count($expected)) {
            return false;
        }

        foreach ($actual as $i => $selfField) {
            if (!$selfField->equals($expected[$i])) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param FieldDefinition[] $definitions
     * @return string[]
     */
    public static function getNamesOfTypeDependencies(array $definitions): array
    {
        $dependencies = [];

        foreach ($definitions as $definition) {
            $dependencies[] = $definition->type->getName();
            $dependencies = array_merge($dependencies, $definition->type->getNamesOfDependencies());
        }

        return array_unique($dependencies);
    }
}
