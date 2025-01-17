<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\Type;

class EndpointParameterDefinition
{
    private const NamePlaceholder = '?';
    private const DescriptionPlaceholder = 'N / A';

    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly Type $type
    ) {
    }

    /**
     * @param array{name?: string, description?: string, type: string} $json
     */
    public static function fromJSON(array $json): self
    {
        $parsedType = (new TypeExpressionParser())->parse($json['type']);
        return new self(
            $json['name'] ?? self::NamePlaceholder,
            $json['description'] ?? self::DescriptionPlaceholder,
            $parsedType
        );
    }
}
