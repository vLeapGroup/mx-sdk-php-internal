<?php

use MultiversX\SmartContracts\ArgSerializer;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\Types\BytesType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\EndpointParameterDefinition;

beforeEach(function () {
    $this->serializer = new ArgSerializer();
});

describe('CompositeValue', function () {
    it('should get valueOf()', function () {
        $compositeType = new CompositeType(
            new U32Type(),
            new BytesType()
        );

        $compositeValue = new CompositeValue(
            $compositeType,
            [
                new U32Value(7),
                BytesValue::fromUTF8('hello')
            ]
        );

        $values = $compositeValue->valueOf();
        expect($values)->toHaveCount(2);
        expect($values[0]->toInt())->toBe(7);
        expect($values[1])->toBe('hello');
    });

    it('should get valueOf() upon decoding', function () {
        $compositeType = new CompositeType(
            new U32Type(),
            new BytesType()
        );

        $endpointDefinition = new EndpointParameterDefinition('', '', $compositeType);

        [$compositeValue] = $this->serializer->stringToValues('2a@abba', [$endpointDefinition]);
        $values = $compositeValue->valueOf();

        expect($values)->toHaveCount(2);
        expect($values[0]->toInt())->toBe(42);
        expect($values[1])->toBe(hex2bin('abba'));
    });

    it('should get valueOf() when items are missing', function () {
        $compositeType = new CompositeType(
            new U32Type(),
            new BytesType()
        );

        $items = [null, null];
        $compositeValue = new CompositeValue($compositeType, $items);

        $values = $compositeValue->valueOf();
        expect($values)->toHaveCount(2);
        expect($values[0])->toBeNull();
        expect($values[1])->toBeNull();
    });

    it('should get valueOf() upon decoding when items are missing', function () {
        $compositeType = new CompositeType(
            new U32Type(),
            new BytesType()
        );

        $endpointDefinition = new EndpointParameterDefinition('', '', $compositeType);

        [$compositeValue] = $this->serializer->stringToValues('', [$endpointDefinition]);
        $values = $compositeValue->valueOf();

        expect($values)->toHaveCount(2);
        expect($values[0]->toInt())->toBe(0);
        expect($values[1])->toBeNull();
    });
});
