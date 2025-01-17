<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\Errors\TypingSystemError;
use MultiversX\SmartContracts\Abi\TypeFormula;
use MultiversX\SmartContracts\Abi\TypeFormulaParser;
use MultiversX\SmartContracts\Typesystem\Types\Type;

class TypeExpressionParser
{
    private TypeFormulaParser $backingTypeFormulaParser;

    public function __construct()
    {
        $this->backingTypeFormulaParser = new TypeFormulaParser();
    }

    public function parse(string $expression): Type
    {
        try {
            return $this->doParse($expression);
        } catch (\Throwable $e) {
            throw new TypingSystemError(
                sprintf('Failed to parse type expression: %s. Error: %s', $expression, $e->getMessage())
            );
        }
    }

    private function doParse(string $expression): Type
    {
        $typeFormula = $this->backingTypeFormulaParser->parseExpression($expression);
        return $this->typeFormulaToType($typeFormula);
    }

    private function typeFormulaToType(TypeFormula $typeFormula): Type
    {
        $typeParameters = array_map(
            fn(TypeFormula $formula) => $this->typeFormulaToType($formula),
            $typeFormula->typeParameters
        );

        return new Type(
            $typeFormula->name,
            $typeParameters,
            null,
            $typeFormula->metadata
        );
    }
}
