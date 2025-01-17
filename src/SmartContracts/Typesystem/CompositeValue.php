<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\Utils\Guards;

class CompositeValue extends TypedValue
{
    public const ClassName = "CompositeValue";

    public function __construct(
        CompositeType $type,
        private readonly array $items
    ) {
        parent::__construct($type);

        Guards::guardLength($items, count($type->getTypeParameters()));

        // TODO: assert type of each item (wrt. type.getTypeParameters()).
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public static function fromItems(TypedValue ...$items): self
    {
        $typeParameters = array_map(
            fn(TypedValue $value) => $value->getType(),
            $items
        );

        $type = new CompositeType(...$typeParameters);
        return new self($type, $items);
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
            fn(?TypedValue $item) => $item?->valueOf(),
            $this->items
        );
    }

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof CompositeValue)) {
            return false;
        }

        if ($this->getType()->differs($other->getType())) {
            return false;
        }

        if (count($this->items) !== count($other->items)) {
            return false;
        }

        for ($i = 0; $i < count($this->items); $i++) {
            $selfItem = $this->items[$i];
            $otherItem = $other->items[$i];

            if (!$selfItem->equals($otherItem)) {
                return false;
            }
        }

        return true;
    }
}
