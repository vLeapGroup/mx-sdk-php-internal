<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\FieldDefinition;

class TupleType extends StructType
{
    public const ClassName = 'TupleType';

    /**
     * @param Type[] $typeParameters
     */
    public function __construct(Type ...$typeParameters)
    {
        parent::__construct(
            self::prepareName($typeParameters),
            self::prepareFieldDefinitions($typeParameters)
        );
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * @param Type[] $typeParameters
     */
    private static function prepareName(array $typeParameters): string
    {
        $fields = implode(', ', array_map(
            fn(Type $type) => $type->getName(),
            $typeParameters
        ));

        return "tuple<{$fields}>";
    }

    /**
     * @param Type[] $typeParameters
     * @return FieldDefinition[]
     */
    private static function prepareFieldDefinitions(array $typeParameters): array
    {
        return array_map(
            fn(Type $type, int $i) => new FieldDefinition(
                self::prepareFieldName($i),
                'anonymous tuple field',
                $type
            ),
            $typeParameters,
            array_keys($typeParameters)
        );
    }

    public static function prepareFieldName(int $fieldIndex): string
    {
        return "field{$fieldIndex}";
    }
}
