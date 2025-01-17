<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\Types\VariadicType;
use MultiversX\SmartContracts\Typesystem\Types\TypePlaceholder;

/**
 * An abstraction that represents a sequence of values held under the umbrella of a variadic input / output parameter.
 *
 * Since at the time of constructing input parameters or decoding output parameters, the length is known,
 * this TypedValue behaves similar to a List.
 */
class VariadicValue extends TypedValue
{
    public const ClassName = "VariadicValue";

    public function __construct(
        VariadicType $type,
        private readonly array $items
    ) {
        parent::__construct($type);
        // TODO: assert items are of type type.getFirstTypeParameter()
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    public static function fromItems(TypedValue ...$items): self
    {
        return self::createFromItems($items, false);
    }

    public static function fromItemsCounted(TypedValue ...$items): self
    {
        return self::createFromItems($items, true);
    }

    private static function createFromItems(array $items, bool $isCounted): self
    {
        if (empty($items)) {
            return new self(new VariadicType(new TypePlaceholder(), $isCounted), []);
        }

        $typeParameter = $items[0]->getType();
        return new self(new VariadicType($typeParameter, $isCounted), $items);
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

    public function equals(TypedValue $other): bool
    {
        if (!($other instanceof VariadicValue)) {
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
