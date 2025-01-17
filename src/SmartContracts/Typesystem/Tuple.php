<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Exceptions\TypingSystemError;
use MultiversX\SmartContracts\Typesystem\Types\TupleType;

class Tuple extends Struct
{
    public const ClassName = 'Tuple';

    public function __construct(
        TupleType $type,
        array $fields
    ) {
        parent::__construct($type, $fields);
    }

    public function getClassName(): string
    {
        return self::ClassName;
    }

    /**
     * @param TypedValue[] $items
     */
    public static function fromItems(array $items): self
    {
        if (count($items) < 1) {
            // TODO: Define a better error.
            throw new TypingSystemError('bad tuple items');
        }

        $fieldsTypes = array_map(
            fn(TypedValue $item) => $item->getType(),
            $items
        );

        $tupleType = new TupleType(...$fieldsTypes);

        $fields = array_map(
            fn(TypedValue $item, int $i) => new Field(
                $item,
                TupleType::prepareFieldName($i)
            ),
            $items,
            array_keys($items)
        );

        return new self($tupleType, $fields);
    }
}
