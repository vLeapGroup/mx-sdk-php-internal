<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

class ManagedDecimalSignedType extends Type
{
    public const ClassName = 'ManagedDecimalSignedType';

    /**
     * @param int|string $metadata
     */
    public function __construct(int|string $metadata)
    {
        parent::__construct('ManagedDecimalSigned', null, null, $metadata);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * @return int|string
     */
    public function getMetadata(): int|string
    {
        return $this->metadata;
    }

    public function isVariable(): bool
    {
        return $this->metadata === 'usize';
    }
}
