<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class ExplicitEnumVariantDefinition
{
    public function __construct(
        public readonly string $name
    ) {
    }

    /**
     * @param array{name: string} $json
     */
    public static function fromJSON(array $json): self
    {
        return new self($json['name']);
    }
}
