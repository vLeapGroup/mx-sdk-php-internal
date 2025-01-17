<?php

namespace MultiversX\SmartContracts\Typesystem;

use MultiversX\SmartContracts\Exceptions\TypingSystemError;
use MultiversX\SmartContracts\Typesystem\Types\AddressType;
use MultiversX\SmartContracts\Typesystem\Types\ArrayVecType;
use MultiversX\SmartContracts\Typesystem\Types\BooleanType;
use MultiversX\SmartContracts\Typesystem\Types\BytesType;
use MultiversX\SmartContracts\Typesystem\Types\CodeMetadataType;
use MultiversX\SmartContracts\Typesystem\Types\EnumType;
use MultiversX\SmartContracts\Typesystem\Types\ExplicitEnumType;
use MultiversX\SmartContracts\Typesystem\Types\H256Type;
use MultiversX\SmartContracts\Typesystem\Types\ListType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalSignedType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalType;
use MultiversX\SmartContracts\Typesystem\Types\NothingType;
use MultiversX\SmartContracts\Typesystem\Types\NumericalType;
use MultiversX\SmartContracts\Typesystem\Types\OptionType;
use MultiversX\SmartContracts\Typesystem\Types\PrimitiveType;
use MultiversX\SmartContracts\Typesystem\Types\StringType;
use MultiversX\SmartContracts\Typesystem\Types\StructType;
use MultiversX\SmartContracts\Typesystem\Types\TokenIdentifierType;
use MultiversX\SmartContracts\Typesystem\Types\TupleType;
use MultiversX\SmartContracts\Typesystem\Types\Type;
use MultiversX\SmartContracts\Typesystem\AddressValue;
use MultiversX\SmartContracts\Typesystem\ArrayVec;
use MultiversX\SmartContracts\Typesystem\BooleanValue;
use MultiversX\SmartContracts\Typesystem\BytesValue;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;
use MultiversX\SmartContracts\Typesystem\EnumValue;
use MultiversX\SmartContracts\Typesystem\ExplicitEnumValue;
use MultiversX\SmartContracts\Typesystem\H256Value;
use MultiversX\SmartContracts\Typesystem\ListValue;
use MultiversX\SmartContracts\Typesystem\ManagedDecimalSignedValue;
use MultiversX\SmartContracts\Typesystem\ManagedDecimalValue;
use MultiversX\SmartContracts\Typesystem\NothingValue;
use MultiversX\SmartContracts\Typesystem\NumericalValue;
use MultiversX\SmartContracts\Typesystem\OptionValue;
use MultiversX\SmartContracts\Typesystem\PrimitiveValue;
use MultiversX\SmartContracts\Typesystem\StringValue;
use MultiversX\SmartContracts\Typesystem\StructValue;
use MultiversX\SmartContracts\Typesystem\TokenIdentifierValue;
use MultiversX\SmartContracts\Typesystem\TupleValue;
use MultiversX\SmartContracts\Typesystem\TypedValue;

class TypeSelectors
{
    /**
     * @template T
     * @param Type $type
     * @param array{
     *     onOption: callable(): T,
     *     onList: callable(): T,
     *     onArray: callable(): T,
     *     onPrimitive: callable(): T,
     *     onStruct: callable(): T,
     *     onTuple: callable(): T,
     *     onEnum: callable(): T,
     *     onExplicitEnum: callable(): T,
     *     onManagedDecimal: callable(): T,
     *     onManagedDecimalSigned: callable(): T,
     *     onOther?: callable(): T
     * } $selectors
     * @return T
     */
    public static function onTypeSelect(Type $type, array $selectors): mixed
    {
        if ($type->hasExactClass(OptionType::ClassName)) {
            return $selectors['onOption']();
        }
        if ($type->hasExactClass(ListType::ClassName)) {
            return $selectors['onList']();
        }
        if ($type->hasExactClass(ArrayVecType::ClassName)) {
            return $selectors['onArray']();
        }
        if ($type->hasClassOrSuperclass(PrimitiveType::ClassName)) {
            return $selectors['onPrimitive']();
        }
        if ($type->hasExactClass(StructType::ClassName)) {
            return $selectors['onStruct']();
        }
        if ($type->hasExactClass(TupleType::ClassName)) {
            return $selectors['onTuple']();
        }
        if ($type->hasExactClass(EnumType::ClassName)) {
            return $selectors['onEnum']();
        }
        if ($type->hasExactClass(ExplicitEnumType::ClassName)) {
            return $selectors['onExplicitEnum']();
        }
        if ($type->hasExactClass(ManagedDecimalType::ClassName)) {
            return $selectors['onManagedDecimal']();
        }
        if ($type->hasExactClass(ManagedDecimalSignedType::ClassName)) {
            return $selectors['onManagedDecimalSigned']();
        }

        if (isset($selectors['onOther'])) {
            return $selectors['onOther']();
        }

        throw new TypingSystemError("type isn't known: " . $type->getName());
    }

    /**
     * @template T
     * @param TypedValue $value
     * @param array{
     *     onPrimitive: callable(): T,
     *     onOption: callable(): T,
     *     onList: callable(): T,
     *     onArray: callable(): T,
     *     onStruct: callable(): T,
     *     onTuple: callable(): T,
     *     onEnum: callable(): T,
     *     onExplicitEnum: callable(): T,
     *     onManagedDecimal: callable(): T,
     *     onManagedDecimalSigned: callable(): T,
     *     onOther?: callable(): T
     * } $selectors
     * @return T
     */
    public static function onTypedValueSelect(TypedValue $value, array $selectors): mixed
    {
        if ($value->hasClassOrSuperclass(PrimitiveValue::ClassName)) {
            return $selectors['onPrimitive']();
        }
        if ($value->hasExactClass(OptionValue::ClassName)) {
            return $selectors['onOption']();
        }
        if ($value->hasExactClass(ListValue::ClassName)) {
            return $selectors['onList']();
        }
        if ($value->hasExactClass(ArrayVec::ClassName)) {
            return $selectors['onArray']();
        }
        if ($value->hasExactClass(Struct::ClassName)) {
            return $selectors['onStruct']();
        }
        if ($value->hasExactClass(Tuple::ClassName)) {
            return $selectors['onTuple']();
        }
        if ($value->hasExactClass(EnumValue::ClassName)) {
            return $selectors['onEnum']();
        }
        if ($value->hasExactClass(ExplicitEnumValue::ClassName)) {
            return $selectors['onExplicitEnum']();
        }
        if ($value->hasExactClass(ManagedDecimalValue::ClassName)) {
            return $selectors['onManagedDecimal']();
        }
        if ($value->hasExactClass(ManagedDecimalSignedValue::ClassName)) {
            return $selectors['onManagedDecimalSigned']();
        }

        if (isset($selectors['onOther'])) {
            return $selectors['onOther']();
        }

        throw new TypingSystemError("value isn't typed: " . $value->getType()->getName());
    }

    /**
     * @template T
     * @param PrimitiveValue $value
     * @param array{
     *     onBoolean: callable(): T,
     *     onNumerical: callable(): T,
     *     onAddress: callable(): T,
     *     onBytes: callable(): T,
     *     onString: callable(): T,
     *     onH256: callable(): T,
     *     onTypeIdentifier: callable(): T,
     *     onCodeMetadata: callable(): T,
     *     onNothing: callable(): T,
     *     onOther?: callable(): T
     * } $selectors
     * @return T
     */
    public static function onPrimitiveValueSelect(PrimitiveValue $value, array $selectors): mixed
    {
        if ($value->hasExactClass(BooleanValue::ClassName)) {
            return $selectors['onBoolean']();
        }
        if ($value->hasClassOrSuperclass(NumericalValue::ClassName)) {
            return $selectors['onNumerical']();
        }
        if ($value->hasExactClass(AddressValue::ClassName)) {
            return $selectors['onAddress']();
        }
        if ($value->hasExactClass(BytesValue::ClassName)) {
            return $selectors['onBytes']();
        }
        if ($value->hasExactClass(StringValue::ClassName)) {
            return $selectors['onString']();
        }
        if ($value->hasExactClass(H256Value::ClassName)) {
            return $selectors['onH256']();
        }
        if ($value->hasExactClass(TokenIdentifierValue::ClassName)) {
            return $selectors['onTypeIdentifier']();
        }
        if ($value->hasExactClass(CodeMetadataValue::ClassName)) {
            return $selectors['onCodeMetadata']();
        }
        if ($value->hasExactClass(NothingValue::ClassName)) {
            return $selectors['onNothing']();
        }

        if (isset($selectors['onOther'])) {
            return $selectors['onOther']();
        }

        throw new TypingSystemError("value isn't a primitive: " . $value->getType()->getName());
    }

    /**
     * @template T
     * @param PrimitiveType $type
     * @param array{
     *     onBoolean: callable(): T,
     *     onNumerical: callable(): T,
     *     onAddress: callable(): T,
     *     onBytes: callable(): T,
     *     onString: callable(): T,
     *     onH256: callable(): T,
     *     onTokenIndetifier: callable(): T,
     *     onCodeMetadata: callable(): T,
     *     onNothing: callable(): T,
     *     onOther?: callable(): T
     * } $selectors
     * @return T
     */
    public static function onPrimitiveTypeSelect(PrimitiveType $type, array $selectors): mixed
    {
        if ($type->hasExactClass(BooleanType::ClassName)) {
            return $selectors['onBoolean']();
        }
        if ($type->hasClassOrSuperclass(NumericalType::ClassName)) {
            return $selectors['onNumerical']();
        }
        if ($type->hasExactClass(AddressType::ClassName)) {
            return $selectors['onAddress']();
        }
        if ($type->hasExactClass(BytesType::ClassName)) {
            return $selectors['onBytes']();
        }
        if ($type->hasExactClass(StringType::ClassName)) {
            return $selectors['onString']();
        }
        if ($type->hasExactClass(H256Type::ClassName)) {
            return $selectors['onH256']();
        }
        if ($type->hasExactClass(TokenIdentifierType::ClassName)) {
            return $selectors['onTokenIndetifier']();
        }
        if ($type->hasExactClass(CodeMetadataType::ClassName)) {
            return $selectors['onCodeMetadata']();
        }
        if ($type->hasExactClass(NothingType::ClassName)) {
            return $selectors['onNothing']();
        }

        if (isset($selectors['onOther'])) {
            return $selectors['onOther']();
        }

        throw new TypingSystemError("type isn't a known primitive: " . $type->getName());
    }
}
