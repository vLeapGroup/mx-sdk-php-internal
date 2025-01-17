<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\Types\CodeMetadataType;

class CodeMetadataValue extends PrimitiveValue
{
    private CodeMetadata $value;

    public function __construct(CodeMetadata $value)
    {
        parent::__construct(new CodeMetadataType());
        $this->value = $value;
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof CodeMetadataValue)) {
            return false;
        }

        return $this->value->equals($other->value);
    }

    public function valueOf(): CodeMetadata
    {
        return $this->value;
    }
}
