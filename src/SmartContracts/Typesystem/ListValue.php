<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\TypePlaceholder;
use MultiversX\SmartContracts\Typesystem\Types\CollectionOfTypedValues;

class ListValue extends TypedValue
{
    public const ClassName = "List";

    public function __construct(
        ListType $type,
        array $items,
        private ?CollectionOfTypedValues $backingCollection = null
    ) {
        parent::__construct($type);

        // TODO: assert items are of type type.getFirstTypeParameter()

        $this->backingCollection = new CollectionOfTypedValues($items);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public static function fromItems(array $items): self
    {
        if (empty($items)) {
            return new self(new ListType(new TypePlaceholder()), []);
        }

        $typeParameter = $items[0]->getType();
        $listType = new ListType($typeParameter);
        return new self($listType, $items);
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
        if (!($other instanceof ListValue)) {
            return false;
        }

        return $this->backingCollection->equals($other->backingCollection);
    }
}
