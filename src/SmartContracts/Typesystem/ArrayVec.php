<?php

namespace MultiversX\SmartContracts\Typesystem;

use InvalidArgumentException;
use MultiversX\SmartContracts\Typesystem\Types\ArrayVecType;
use MultiversX\SmartContracts\Typesystem\Types\CollectionOfTypedValues;

class ArrayVec extends TypedValue
{
    public const ClassName = 'ArrayVec';
    private CollectionOfTypedValues $backingCollection;

    /**
     * @param ArrayVecType $type
     * @param TypedValue[] $items
     */
    public function __construct(ArrayVecType $type, array $items)
    {
        parent::__construct($type);

        if (count($items) !== $type->length) {
            throw new InvalidArgumentException('items length must match type length');
        }

        $this->backingCollection = new CollectionOfTypedValues($items);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public function getLength(): int
    {
        return $this->backingCollection->getLength();
    }

    /**
     * @return TypedValue[]
     */
    public function getItems(): array
    {
        return $this->backingCollection->getItems();
    }

    public function valueOf(): array
    {
        return $this->backingCollection->valueOf();
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof ArrayVec)) {
            return false;
        }

        return $this->backingCollection->equals($other->backingCollection);
    }
}
