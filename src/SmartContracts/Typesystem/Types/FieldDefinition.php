<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Parser\TypeExpressionParser;

class FieldDefinition
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly Type $type
    ) {
    }

    /**
     * @param array{name: string, description: string, type: string} $json
     */
    public static function fromJSON(array $json): FieldDefinition
    {
        $parsedType = (new TypeExpressionParser())->parse($json['type']);
        return new FieldDefinition($json['name'], $json['description'], $parsedType);
    }
}
