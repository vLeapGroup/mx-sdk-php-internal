<?php

use MultiversX\Errors\TypingSystemError;
use MultiversX\SmartContracts\Typesystem\TypeExpressionParser;

beforeEach(function () {
    $this->parser = new TypeExpressionParser();
});

describe('TypeExpressionParser', function () {
    it('should parse simple types', function () {
        $type = $this->parser->parse('u32');

        expect($type->toJSON())->toBe([
            'name' => 'u32',
            'typeParameters' => [],
        ]);
    });

    it('should parse nested types', function () {
        $type = $this->parser->parse('List<u32>');

        expect($type->toJSON())->toBe([
            'name' => 'List',
            'typeParameters' => [
                [
                    'name' => 'u32',
                    'typeParameters' => [],
                ],
            ],
        ]);
    });

    it('should parse complex nested types', function () {
        $type = $this->parser->parse('Option<List<Address>>');

        expect($type->toJSON())->toBe([
            'name' => 'Option',
            'typeParameters' => [
                [
                    'name' => 'List',
                    'typeParameters' => [
                        [
                            'name' => 'Address',
                            'typeParameters' => [],
                        ],
                    ],
                ],
            ],
        ]);
    });

    it('should parse multiple type parameters', function () {
        $type = $this->parser->parse('MultiArg<bytes, Address>');

        expect($type->toJSON())->toBe([
            'name' => 'MultiArg',
            'typeParameters' => [
                [
                    'name' => 'bytes',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'Address',
                    'typeParameters' => [],
                ],
            ],
        ]);
    });

    it('should parse tuples', function () {
        $type = $this->parser->parse('tuple2<i32, bytes>');

        expect($type->toJSON())->toBe([
            'name' => 'tuple2',
            'typeParameters' => [
                [
                    'name' => 'i32',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'bytes',
                    'typeParameters' => [],
                ],
            ],
        ]);
    });

    it('should parse complex tuples', function () {
        $type = $this->parser->parse('tuple3<i32, bytes, Option<i64>>');

        expect($type->toJSON())->toBe([
            'name' => 'tuple3',
            'typeParameters' => [
                [
                    'name' => 'i32',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'bytes',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'Option',
                    'typeParameters' => [
                        [
                            'name' => 'i64',
                            'typeParameters' => [],
                        ],
                    ],
                ],
            ],
        ]);
    });

    it('should parse types with spaces', function () {
        $type = $this->parser->parse('multi<u8, utf-8 string, u8>');

        expect($type->toJSON())->toBe([
            'name' => 'multi',
            'typeParameters' => [
                [
                    'name' => 'u8',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'utf-8 string',
                    'typeParameters' => [],
                ],
                [
                    'name' => 'u8',
                    'typeParameters' => [],
                ],
            ],
        ]);
    });

    it('should throw on invalid expressions', function () {
        expect(fn() => $this->parser->parse('<>'))->toThrow(TypingSystemError::class);
        expect(fn() => $this->parser->parse('<'))->toThrow(TypingSystemError::class);
        expect(fn() => $this->parser->parse('MultiResultVec<MultiResult2<Address, u64>'))->toThrow(TypingSystemError::class);
        expect(fn() => $this->parser->parse('a, b'))->toThrow(TypingSystemError::class);
    });
});
