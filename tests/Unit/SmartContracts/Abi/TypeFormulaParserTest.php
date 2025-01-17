<?php

use MultiversX\SmartContracts\Abi\TypeFormulaParser;

describe('TypeFormulaParser', function () {
    it('should parse expressions', function () {
        $parser = new TypeFormulaParser();

        $testVectors = [
            ['i64', 'i64'],
            ['  i64  ', 'i64'],
            ['utf-8 string', 'utf-8 string'],
            ['MultiResultVec<MultiResult2<Address, u64>>', 'MultiResultVec<MultiResult2<Address, u64>>'],
            ['tuple3<i32, bytes, Option<i64>>', 'tuple3<i32, bytes, Option<i64>>'],
            ['tuple2<i32, i32>', 'tuple2<i32, i32>'],
            ['tuple2<i32,i32>  ', 'tuple2<i32, i32>'],
            ['tuple<List<u64>, List<u64>>', 'tuple<List<u64>, List<u64>>'],
        ];

        foreach ($testVectors as [$inputExpression, $expectedExpression]) {
            $typeFormula = $parser->parseExpression($inputExpression);
            $outputExpression = (string)$typeFormula;

            expect($outputExpression)->toBe($expectedExpression);
        }
    });
});
