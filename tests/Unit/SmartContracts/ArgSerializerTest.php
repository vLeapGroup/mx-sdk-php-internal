<?php

use MultiversX\SmartContracts\ArgSerializer;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\EndpointParameterDefinition;
use MultiversX\SmartContracts\Typesystem\I16Value;
use MultiversX\SmartContracts\Typesystem\I32Value;
use MultiversX\SmartContracts\Typesystem\I64Value;
use MultiversX\SmartContracts\Typesystem\ListValue;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\Tuple;
use MultiversX\SmartContracts\Typesystem\TypeExpressionParser;
use MultiversX\SmartContracts\Typesystem\TypeMapper;
use MultiversX\SmartContracts\Typesystem\U16Value;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\U8Value;
use MultiversX\SmartContracts\Typesystem\VariadicValue;

beforeEach(function () {
    $this->serializer = new ArgSerializer();
    $this->typeParser = new TypeExpressionParser();
    $this->typeMapper = new TypeMapper();
});

describe('ArgSerializer', function () {
    it('should serialize and deserialize basic types', function () {
        serializeThenDeserialize(
            $this->serializer,
            $this->typeParser,
            $this->typeMapper,
            ['u32', 'i64', 'bytes'],
            [
                new U32Value(100),
                new I64Value('-1'),
                BytesValue::fromHex('abba')
            ],
            '64@ff@abba'
        );
    });

    it('should serialize and deserialize optional and composite types', function () {
        serializeThenDeserialize(
            $this->serializer,
            $this->typeParser,
            $this->typeMapper,
            ['Option<u32>', 'Option<u8>', 'MultiArg<u8, bytes>'],
            [
                OptionValue::newProvided(new U32Value(100)),
                OptionValue::newMissing(),
                CompositeValue::fromItems(
                    new U8Value(3),
                    BytesValue::fromHex('abba')
                )
            ],
            '0100000064@@03@abba'
        );
    });

    it('should serialize and deserialize list and variadic types', function () {
        serializeThenDeserialize(
            $this->serializer,
            $this->typeParser,
            $this->typeMapper,
            ['MultiArg<List<u16>>', 'VarArgs<bytes>'],
            [
                CompositeValue::fromItems(
                    ListValue::fromItems([new U16Value(8), new U16Value(9)])
                ),
                VariadicValue::fromItems(
                    BytesValue::fromHex('abba'),
                    BytesValue::fromHex('abba'),
                    BytesValue::fromHex('abba')
                )
            ],
            '00080009@abba@abba@abba'
        );
    });

    it('should serialize and deserialize complex composite types', function () {
        serializeThenDeserialize(
            $this->serializer,
            $this->typeParser,
            $this->typeMapper,
            ['MultiArg<Option<u8>, List<u16>>', 'VarArgs<bytes>'],
            [
                CompositeValue::fromItems(
                    OptionValue::newProvided(new U8Value(7)),
                    ListValue::fromItems([new U16Value(8), new U16Value(9)])
                ),
                VariadicValue::fromItems(
                    BytesValue::fromHex('abba'),
                    BytesValue::fromHex('abba'),
                    BytesValue::fromHex('abba')
                )
            ],
            '0107@00080009@abba@abba@abba'
        );
    });

    it('should serialize and deserialize tuples', function () {
        serializeThenDeserialize(
            $this->serializer,
            $this->typeParser,
            $this->typeMapper,
            ['tuple2<i32, i16>'],
            [
                Tuple::fromItems([new I32Value(100), new I16Value(10)])
            ],
            '00000064000a'
        );
    });
});

function serializeThenDeserialize(
    ArgSerializer $serializer,
    TypeExpressionParser $typeParser,
    TypeMapper $typeMapper,
    array $typeExpressions,
    array $values,
    string $expectedJoinedString
): void {
    $types = array_map(
        fn($type) => $typeMapper->mapType($type),
        array_map(
            fn($expression) => $typeParser->parse($expression),
            $typeExpressions
        )
    );

    $endpointDefinitions = array_map(
        fn($type) => new EndpointParameterDefinition('foo', 'bar', $type),
        $types
    );

    // values => joined string
    $result = $serializer->valuesToString($values);
    $actualJoinedString = $result['argumentsString'];
    expect($actualJoinedString)->toBe($expectedJoinedString);

    // joined string => values
    $decodedValues = $serializer->stringToValues($actualJoinedString, $endpointDefinitions);

    // Now let's check for equality
    expect($decodedValues)->toHaveCount(count($values));

    foreach ($values as $i => $value) {
        expect($decodedValues[$i]->valueOf())->toEqual($value->valueOf(), "index = $i");
    }
}
