<?php

namespace MultiversX\SmartContracts\Abi;

class TypeFormulaParser
{
    private const BEGIN_TYPE_PARAMETERS = '<';
    private const END_TYPE_PARAMETERS = '>';
    private const COMMA = ',';
    private const PUNCTUATION = [
        self::COMMA,
        self::BEGIN_TYPE_PARAMETERS,
        self::END_TYPE_PARAMETERS,
    ];

    public function parseExpression(string $expression): TypeFormula
    {
        $expression = trim($expression);
        $tokens = array_filter(
            $this->tokenizeExpression($expression),
            fn($token) => $token !== self::COMMA
        );
        $stack = [];

        foreach ($tokens as $token) {
            if ($this->isPunctuation($token)) {
                if ($this->isEndOfTypeParameters($token)) {
                    $typeFormula = $this->acquireTypeWithParameters($stack);
                    $stack[] = $typeFormula;
                } elseif ($this->isBeginningOfTypeParameters($token)) {
                    // This symbol is pushed as a simple string.
                    $stack[] = $token;
                } else {
                    throw new \Exception("Unexpected token (punctuation): {$token}");
                }
            } else {
                // It's a type name. We push it as a simple string.
                $stack[] = $token;
            }
        }

        if (count($stack) !== 1) {
            throw new \Exception("Unexpected stack length at end of parsing: " . count($stack));
        }

        if (in_array($stack[0], self::PUNCTUATION, true)) {
            throw new \Exception('Unexpected root element.');
        }

        $item = $stack[0];

        if ($item instanceof TypeFormula) {
            return $item;
        }

        if (is_string($item)) {
            // Expression contained a simple, non-generic type.
            return new TypeFormula($item, []);
        }

        throw new \Exception("Unexpected item on stack: " . print_r($item, true));
    }

    private function tokenizeExpression(string $expression): array
    {
        $tokens = [];
        $currentToken = '';

        foreach (str_split($expression) as $character) {
            if ($this->isPunctuation($character)) {
                if ($currentToken !== '') {
                    // Retain current token
                    $tokens[] = trim($currentToken);
                    // Reset current token
                    $currentToken = '';
                }

                // Punctuation character
                $tokens[] = $character;
            } else {
                $currentToken .= $character;
            }
        }

        if ($currentToken !== '') {
            // Retain the last token (if any).
            $tokens[] = trim($currentToken);
        }

        return $tokens;
    }

    private function acquireTypeWithParameters(array &$stack): TypeFormula
    {
        $typeParameters = $this->acquireTypeParameters($stack);
        $typeName = array_pop($stack);

        if ($typeName === 'ManagedDecimal' || $typeName === 'ManagedDecimalSigned') {
            $metadata = $typeParameters[0]->name;
            return new TypeFormula($typeName, [], $metadata);
        }

        return new TypeFormula($typeName, array_reverse($typeParameters));
    }

    private function acquireTypeParameters(array &$stack): array
    {
        $typeParameters = [];

        while (true) {
            $item = array_pop($stack);
            if ($item === null) {
                throw new \Exception('Badly specified type parameters');
            }

            if ($this->isBeginningOfTypeParameters($item)) {
                // We've acquired all type parameters.
                break;
            }

            if ($item instanceof TypeFormula) {
                // Type parameter is a previously-acquired type.
                $typeParameters[] = $item;
            } elseif (is_string($item)) {
                // Type parameter is a simple, non-generic type.
                $typeParameters[] = new TypeFormula($item, []);
            } else {
                throw new \Exception("Unexpected type parameter object in stack: " . print_r($item, true));
            }
        }

        return $typeParameters;
    }

    private function isPunctuation(string $token): bool
    {
        return in_array($token, self::PUNCTUATION, true);
    }

    private function isEndOfTypeParameters(string $token): bool
    {
        return $token === self::END_TYPE_PARAMETERS;
    }

    private function isBeginningOfTypeParameters(string $token): bool
    {
        return $token === self::BEGIN_TYPE_PARAMETERS;
    }
}
