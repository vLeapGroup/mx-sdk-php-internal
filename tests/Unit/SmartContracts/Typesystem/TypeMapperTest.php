<?php

namespace Tests\Unit\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Typesystem\TypeExpressionParser;
use MultiversX\SmartContracts\Typesystem\TypeMapper;
use MultiversX\SmartContracts\Typesystem\Types\AddressType;
use MultiversX\SmartContracts\Typesystem\Types\ArrayVecType;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\BytesType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\Types\I32Type;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;
use MultiversX\SmartContracts\Typesystem\Types\TupleType;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\U16Type;
use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use MultiversX\SmartContracts\Typesystem\Types\U8Type;
use MultiversX\SmartContracts\Typesystem\Types\VariadicType;

beforeEach(function () {
    $this->parser = new TypeExpressionParser();
    $this->mapper = new TypeMapper();
});

describe('TypeMapper', function () {
    it('should map primitive types', function () {
        testMapping($this->parser, $this->mapper, 'u8', new U8Type());
        testMapping($this->parser, $this->mapper, 'u16', new U16Type());
        testMapping($this->parser, $this->mapper, 'u32', new U32Type());
        testMapping($this->parser, $this->mapper, 'u64', new U64Type());
        testMapping($this->parser, $this->mapper, 'BigUint', new BigUIntType());
        testMapping($this->parser, $this->mapper, 'TokenIdentifier', new TokenIdentifierType());
    });

    it('should map generic types', function () {
        testMapping($this->parser, $this->mapper, 'Option<u64>', new OptionType(new U64Type()));
        testMapping($this->parser, $this->mapper, 'List<u64>', new ListType(new U64Type()));
    });

    it('should map variadic types', function () {
        testMapping($this->parser, $this->mapper, 'VarArgs<u32>', new VariadicType(new U32Type()));
        testMapping($this->parser, $this->mapper, 'VarArgs<bytes>', new VariadicType(new BytesType()));
        testMapping($this->parser, $this->mapper, 'MultiResultVec<u32>', new VariadicType(new U32Type()));
        testMapping($this->parser, $this->mapper, 'MultiResultVec<Address>', new VariadicType(new AddressType()));
    });

    it('should map complex generic, composite, variadic types', function () {
        testMapping(
            $this->parser,
            $this->mapper,
            'MultiResultVec<MultiResult<i32,bytes,>>',
            new VariadicType(new CompositeType(new I32Type(), new BytesType()))
        );
        testMapping(
            $this->parser,
            $this->mapper,
            'VarArgs<MultiArg<i32,bytes,>>',
            new VariadicType(new CompositeType(new I32Type(), new BytesType()))
        );
        testMapping($this->parser, $this->mapper, 'OptionalResult<Address>', new OptionalType(new AddressType()));
    });

    it('should map tuples', function () {
        testMapping($this->parser, $this->mapper, 'tuple2<u32,bytes>', new TupleType(new U32Type(), new BytesType()));
        testMapping($this->parser, $this->mapper, 'tuple2<Address,BigUint>', new TupleType(new AddressType(), new BigUIntType()));
        testMapping($this->parser, $this->mapper, 'tuple3<u32, bytes, u64>', new TupleType(new U32Type(), new BytesType(), new U64Type()));
    });

    it('should map arrays', function () {
        testArrayMapping($this->parser, $this->mapper, 'array2<BigUint>', 2, new BigUIntType());
        testArrayMapping($this->parser, $this->mapper, 'array2<u32>', 2, new U32Type());
        testArrayMapping($this->parser, $this->mapper, 'array6<u8>', 6, new U8Type());
        testArrayMapping($this->parser, $this->mapper, 'array8<BigUint>', 8, new BigUIntType());
        testArrayMapping($this->parser, $this->mapper, 'array48<u8>', 48, new U8Type());
        testArrayMapping($this->parser, $this->mapper, 'array256<BigUint>', 256, new BigUIntType());
    });
});

function testArrayMapping(TypeExpressionParser $parser, TypeMapper $mapper, string $expression, int $size, Type $typeParameter): void
{
    $type = $parser->parse($expression);
    $mappedType = $mapper->mapType($type);

    expect($mappedType)->toBeInstanceOf(ArrayVecType::class);
    expect($mappedType)->toEqual(new ArrayVecType($size, $typeParameter));
}

function testMapping(TypeExpressionParser $parser, TypeMapper $mapper, string $expression, Type $expectedType): void
{
    $type = $parser->parse($expression);
    $mappedType = $mapper->mapType($type);

    expect($mappedType)->toBeInstanceOf($expectedType::class);
    expect($mappedType)->toEqual($expectedType);
}
