<?php

namespace MultiversX\SmartContracts;

use MultiversX\Constants;
use MultiversX\SmartContracts\Codec\BinaryCodec;
use MultiversX\SmartContracts\Codec\ICodec;
use MultiversX\SmartContracts\Typesystem\CompositeValue;
use MultiversX\SmartContracts\Typesystem\OptionalValue;
use MultiversX\SmartContracts\Typesystem\TypedValue;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use MultiversX\SmartContracts\Typesystem\Types\VariadicType;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Typesystem\VariadicValue;

class ArgSerializer
{
    public function __construct(
        private readonly ICodec $codec = new BinaryCodec()
    ) {
    }

    /**
     * Reads typed values from an arguments string (e.g. aa@bb@@cc), given parameter definitions.
     */
    public function stringToValues(string $joinedString, array $parameters): array
    {
        $buffers = $this->stringToBuffers($joinedString);
        return $this->buffersToValues($buffers, $parameters);
    }

    /**
     * Reads raw buffers from an arguments string (e.g. aa@bb@@cc).
     */
    public function stringToBuffers(string $joinedString): array
    {
        // We also keep the zero-length buffers (they could encode missing options, Option<T>).
        return array_map(
            fn(string $item) => hex2bin($item),
            explode(Constants::ARGUMENTS_SEPARATOR, $joinedString)
        );
    }

    /**
     * Decodes a set of buffers into a set of typed values, given parameter definitions.
     */
    public function buffersToValues(array $buffers, array $parameters): array
    {
        $buffers = $buffers ?: [];
        $values = [];
        $bufferIndex = 0;
        $numBuffers = count($buffers);

        $readValue = function (Type $type) use (&$readValue, &$bufferIndex, &$numBuffers, $buffers) {
            if ($type->hasExactClass(OptionalType::ClassName)) {
                $typedValue = $readValue($type->getFirstTypeParameter());
                return new OptionalValue($type, $typedValue);
            }

            if ($type->hasExactClass(VariadicType::ClassName)) {
                return $this->readVariadicValue($type, $readValue, $bufferIndex, $numBuffers);
            }

            if ($type->hasExactClass(CompositeType::ClassName)) {
                $typedValues = [];

                foreach ($type->getTypeParameters() as $typeParameter) {
                    $typedValues[] = $readValue($typeParameter);
                }

                return new CompositeValue($type, $typedValues);
            }

            // Non-composite (singular), non-variadic (fixed) type.
            return $this->decodeNextBuffer($type, $buffers, $bufferIndex, $numBuffers);
        };

        foreach ($parameters as $parameter) {
            $values[] = $readValue($parameter->type);
        }

        return $values;
    }

    private function readVariadicValue(VariadicType $type, callable $readValue, int &$bufferIndex, int $numBuffers): TypedValue
    {
        $typedValues = [];

        if ($type->isCounted) {
            $count = $readValue(new U32Type())->valueOf()->toNumber();

            for ($i = 0; $i < $count; $i++) {
                $typedValues[] = $readValue($type->getFirstTypeParameter());
            }
        } else {
            while ($bufferIndex < $numBuffers) {
                $typedValues[] = $readValue($type->getFirstTypeParameter());
            }
        }

        return new VariadicValue($type, $typedValues);
    }

    private function decodeNextBuffer(Type $type, array $buffers, int &$bufferIndex, int $numBuffers): ?TypedValue
    {
        if ($bufferIndex >= $numBuffers) {
            return null;
        }

        $buffer = $buffers[$bufferIndex++];
        return $this->codec->decodeTopLevel($buffer, $type);
    }

    /**
     * Serializes a set of typed values into an arguments string (e.g. aa@bb@@cc).
     */
    public function valuesToString(array $values): array
    {
        $strings = $this->valuesToStrings($values);
        return [
            'argumentsString' => implode(Constants::ARGUMENTS_SEPARATOR, $strings),
            'count' => count($strings)
        ];
    }

    /**
     * Serializes a set of typed values into a set of strings.
     */
    public function valuesToStrings(array $values): array
    {
        $buffers = $this->valuesToBuffers($values);
        return array_map(fn($buffer) => bin2hex($buffer), $buffers);
    }

    /**
     * Serializes a set of typed values into a set of strings buffers.
     * Variadic types and composite types might result into none, one or more buffers.
     */
    public function valuesToBuffers(array $values): array
    {
        $buffers = [];

        $handleValue = function (TypedValue $value) use (&$handleValue, &$buffers) {
            if ($value->hasExactClass(OptionalValue::ClassName)) {
                /** @var OptionalValue $value */
                if ($value->isSet()) {
                    $handleValue($value->getTypedValue());
                }
                return;
            }

            if ($value->hasExactClass(VariadicValue::ClassName)) {
                $this->handleVariadicValue($value, $handleValue, $buffers);
                return;
            }

            if ($value->hasExactClass(CompositeValue::ClassName)) {
                /** @var CompositeValue $value */
                foreach ($value->getItems() as $item) {
                    $handleValue($item);
                }
                return;
            }

            // Non-composite (singular), non-variadic (fixed) type.
            $buffers[] = $this->codec->encodeTopLevel($value);
        };

        foreach ($values as $value) {
            $handleValue($value);
        }

        return $buffers;
    }

    private function handleVariadicValue(VariadicValue $value, callable $handleValue, array &$buffers): void
    {
        /** @var VariadicType $variadicType */
        $variadicType = $value->getType();

        if ($variadicType->isCounted) {
            $countValue = new U32Value(count($value->getItems()));
            $buffers[] = $this->codec->encodeTopLevel($countValue);
        }

        foreach ($value->getItems() as $item) {
            $handleValue($item);
        }
    }
}
