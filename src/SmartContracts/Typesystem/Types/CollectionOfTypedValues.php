<?php

namespace MultiversX\SmartContracts\Typesystem\Types;

use MultiversX\SmartContracts\Typesystem\TypedValue;

class CollectionOfTypedValues
{
    public function __construct(
        private readonly array $items
    ) {
    }

    public function getLength(): int
    {
        return count($this->items);
    }

    /**
     * @return TypedValue[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function valueOf(): array
    {
        return array_map(
            fn(TypedValue $item) => $item->valueOf(),
            $this->items
        );
    }

    public function equals(CollectionOfTypedValues $other): bool
    {
        if ($this->getLength() != $other->getLength()) {
            return false;
        }

        for ($i = 0; $i < $this->getLength(); $i++) {
            $selfItem = $this->items[$i];
            $otherItem = $other->items[$i];

            if (!$selfItem->equals($otherItem)) {
                return false;
            }
        }

        return true;
    }
}
