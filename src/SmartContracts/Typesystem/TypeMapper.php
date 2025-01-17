<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Exceptions\TypingSystemError;
use MultiversX\SmartContracts\Typesystem\Types\AddressType;
use MultiversX\SmartContracts\Typesystem\Types\ArrayVecType;
use MultiversX\SmartContracts\Typesystem\Types\BigIntType;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\BooleanType;
use MultiversX\SmartContracts\Typesystem\Types\BytesType;
use MultiversX\SmartContracts\Typesystem\Types\CodeMetadataType;
use MultiversX\SmartContracts\Typesystem\Types\CompositeType;
use MultiversX\SmartContracts\Typesystem\Types\CustomType;
use MultiversX\SmartContracts\Typesystem\Types\EnumType;
use MultiversX\SmartContracts\Typesystem\Types\EnumVariantDefinition;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumType;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumVariantDefinition;
use MultiversX\SmartContracts\Typesystem\Types\H256Type;
use MultiversX\SmartContracts\Typesystem\Types\I16Type;
use MultiversX\SmartContracts\Typesystem\Types\I32Type;
use MultiversX\SmartContracts\Typesystem\Types\I64Type;
use MultiversX\SmartContracts\Typesystem\Types\I8Type;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalSignedType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalType;
use MultiversX\SmartContracts\Typesystem\Types\NothingType;
use MultiversX\SmartContracts\Typesystem\Types\OptionalType;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\StringType;
use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;
use MultiversX\SmartContracts\Typesystem\Types\TupleType;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\Types\U16Type;
use MultiversX\SmartContracts\Typesystem\Types\U32Type;
use MultiversX\SmartContracts\Typesystem\Types\U64Type;
use MultiversX\SmartContracts\Typesystem\Types\U8Type;
use MultiversX\SmartContracts\Typesystem\Types\VariadicType;

class TypeMapper
{
    /** @var array<string, callable> */
    private array $openTypesFactories;

    /** @var array<string, Type> */
    private array $closedTypesMap;

    /** @var array<string, Type> */
    private array $learnedTypesMap;

    /**
     * @param CustomType[] $learnedTypes
     */
    public function __construct(array $learnedTypes = [])
    {
        $this->initializeOpenTypesFactories();
        $this->initializeClosedTypesMap();
        $this->learnedTypesMap = [];

        // Bootstrap from previously learned types, if any
        foreach ($learnedTypes as $type) {
            if ($type->getName() === 'ManagedDecimal' || $type->getName() === 'ManagedDecimalSigned') {
                $this->learnedTypesMap["{$type->getName()}_{$type->getMetadata()}"] = $type;
            } else {
                $this->learnedTypesMap[$type->getName()] = $type;
            }
        }
    }

    private function initializeOpenTypesFactories(): void
    {
        $this->openTypesFactories = [
            'Option' => fn(Type ...$typeParameters) => new OptionType($typeParameters[0]),
            'List' => fn(Type ...$typeParameters) => new ListType($typeParameters[0]),
            'VarArgs' => fn(Type ...$typeParameters) => new VariadicType($typeParameters[0]),
            'MultiResultVec' => fn(Type ...$typeParameters) => new VariadicType($typeParameters[0]),
            'variadic' => fn(Type ...$typeParameters) => new VariadicType($typeParameters[0]),
            'counted-variadic' => fn(Type ...$typeParameters) => new VariadicType($typeParameters[0], true),
            'OptionalArg' => fn(Type ...$typeParameters) => new OptionalType($typeParameters[0]),
            'optional' => fn(Type ...$typeParameters) => new OptionalType($typeParameters[0]),
            'OptionalResult' => fn(Type ...$typeParameters) => new OptionalType($typeParameters[0]),
            'multi' => fn(Type ...$typeParameters) => new CompositeType(...$typeParameters),
            'MultiArg' => fn(Type ...$typeParameters) => new CompositeType(...$typeParameters),
            'MultiResult' => fn(Type ...$typeParameters) => new CompositeType(...$typeParameters),
            'tuple' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple2' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple3' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple4' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple5' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple6' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple7' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'tuple8' => fn(Type ...$typeParameters) => new TupleType(...$typeParameters),
            'array2' => fn(Type ...$typeParameters) => new ArrayVecType(2, $typeParameters[0]),
            'array6' => fn(Type ...$typeParameters) => new ArrayVecType(6, $typeParameters[0]),
            'array8' => fn(Type ...$typeParameters) => new ArrayVecType(8, $typeParameters[0]),
            'array16' => fn(Type ...$typeParameters) => new ArrayVecType(16, $typeParameters[0]),
            'array20' => fn(Type ...$typeParameters) => new ArrayVecType(20, $typeParameters[0]),
            'array32' => fn(Type ...$typeParameters) => new ArrayVecType(32, $typeParameters[0]),
            'array46' => fn(Type ...$typeParameters) => new ArrayVecType(46, $typeParameters[0]),
            'array48' => fn(Type ...$typeParameters) => new ArrayVecType(48, $typeParameters[0]),
            'array64' => fn(Type ...$typeParameters) => new ArrayVecType(64, $typeParameters[0]),
            'array128' => fn(Type ...$typeParameters) => new ArrayVecType(128, $typeParameters[0]),
            'array256' => fn(Type ...$typeParameters) => new ArrayVecType(256, $typeParameters[0]),
            'ManagedDecimal' => fn(mixed $metadata) => new ManagedDecimalType($metadata),
            'ManagedDecimalSigned' => fn(mixed $metadata) => new ManagedDecimalSignedType($metadata),
        ];
    }

    private function initializeClosedTypesMap(): void
    {
        $this->closedTypesMap = [
            'u8' => new U8Type(),
            'u16' => new U16Type(),
            'u32' => new U32Type(),
            'u64' => new U64Type(),
            'U64' => new U64Type(),
            'BigUint' => new BigUIntType(),
            'i8' => new I8Type(),
            'i16' => new I16Type(),
            'i32' => new I32Type(),
            'i64' => new I64Type(),
            'Bigint' => new BigIntType(),
            'BigInt' => new BigIntType(),
            'bool' => new BooleanType(),
            'bytes' => new BytesType(),
            'Address' => new AddressType(),
            'H256' => new H256Type(),
            'utf-8 string' => new StringType(),
            'TokenIdentifier' => new TokenIdentifierType(),
            'EgldOrEsdtTokenIdentifier' => new TokenIdentifierType(),
            'CodeMetadata' => new CodeMetadataType(),
            'nothing' => new NothingType(),
            'AsyncCall' => new NothingType(),
        ];
    }

    public function mapType(Type $type): Type
    {
        $mappedType = $this->mapTypeRecursively($type);

        if ($mappedType) {
            if (!$mappedType->isGenericType()) {
                $this->learnType($mappedType);
            }

            return $mappedType;
        }

        throw new TypingSystemError("Cannot map the type \"{$type->getName()}\" to a known type");
    }

    private function mapTypeRecursively(Type $type): ?Type
    {
        $isGeneric = $type->isGenericType();
        $hasMetadata = $type->hasMetadata();

        if (isset($this->learnedTypesMap[$type->getName()])) {
            return $this->learnedTypesMap[$type->getName()];
        }

        if (isset($this->closedTypesMap[$type->getName()])) {
            return $this->closedTypesMap[$type->getName()];
        }

        if ($type instanceof EnumType) {
            return $this->mapEnumType($type);
        }

        if ($type instanceof ExplicitEnumType) {
            return $this->mapExplicitEnumType($type);
        }

        if ($type instanceof StructType) {
            return $this->mapStructType($type);
        }

        if ($isGeneric || $hasMetadata) {
            return $this->mapGenericType($type);
        }

        return null;
    }

    private function learnType(Type $type): void
    {
        if ($type->getName() === 'ManagedDecimal' || $type->getName() === 'ManagedDecimalSigned') {
            $key = "{$type->getName()}_{$type->getMetadata()}";
            unset($this->learnedTypesMap[$key]);
            $this->learnedTypesMap[$key] = $type;
        } else {
            $key = $type->getName();
            unset($this->learnedTypesMap[$key]);
            $this->learnedTypesMap[$key] = $type;
        }
    }

    private function mapStructType(StructType $type): StructType
    {
        $mappedFields = $this->mappedFields($type->getFieldsDefinitions());
        return new StructType($type->getName(), $mappedFields);
    }

    private function mapEnumType(EnumType $type): EnumType
    {
        $variants = array_map(
            fn($variant) => new EnumVariantDefinition(
                $variant->name,
                $variant->discriminant,
                $this->mappedFields($variant->getFieldsDefinitions())
            ),
            $type->variants
        );
        return new EnumType($type->getName(), $variants);
    }

    private function mapExplicitEnumType(ExplicitEnumType $type): ExplicitEnumType
    {
        $variants = array_map(
            fn($variant) => new ExplicitEnumVariantDefinition($variant->name),
            $type->variants
        );
        return new ExplicitEnumType($type->getName(), $variants);
    }

    /**
     * @param FieldDefinition[] $definitions
     * @return FieldDefinition[]
     */
    private function mappedFields(array $definitions): array
    {
        return array_map(
            fn($definition) => new FieldDefinition(
                $definition->name,
                $definition->description,
                $this->mapType($definition->type)
            ),
            $definitions
        );
    }

    private function mapGenericType(Type $type): Type
    {
        $typeParameters = $type->getTypeParameters();
        $mappedTypeParameters = array_map([$this, 'mapType'], $typeParameters);

        if (!isset($this->openTypesFactories[$type->getName()])) {
            throw new TypingSystemError("Cannot map the generic type \"{$type->getName()}\" to a known type");
        }

        $factory = $this->openTypesFactories[$type->getName()];

        if ($type->hasMetadata()) {
            return $factory($type->getMetadata());
        }

        return $factory(...$mappedTypeParameters);
    }
}
