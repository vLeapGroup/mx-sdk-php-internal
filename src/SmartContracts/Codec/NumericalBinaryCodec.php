<?php

namespace MultiversX\SmartContracts\Codec;

use Brick\Math\BigInteger;
use MultiversX\SmartContracts\Typesystem\Types\NumericalType;
use MultiversX\SmartContracts\Typesystem\NumericalValue;
use MultiversX\SmartContracts\Codec\Utils\BufferUtil;

/**
 * Encodes and decodes "NumericalValue" objects.
 */
class NumericalBinaryCodec implements ICodec
{
    private const SIZE_OF_U32 = 4;

    public function decodeNested(string $buffer, NumericalType $type): array
    {
        $offset = 0;
        $length = $type->getSizeInBytes();

        if (!$length) {
            // Size of type is not known: arbitrary-size big integer.
            // Therefore, we must read the length from the header.
            $offset = self::SIZE_OF_U32;
            $length = unpack('N', $buffer)[1];
        }

        $payload = substr($buffer, $offset, $length);
        $result = $this->decodeTopLevel($payload, $type);
        $decodedLength = $length + $offset;
        return [$result, $decodedLength];
    }

    public function decodeTopLevel(string $buffer, NumericalType $type): NumericalValue
    {
        $payload = $buffer;
        $empty = strlen($buffer) == 0;

        if ($empty) {
            return new NumericalValue($type, BigInteger::zero());
        }

        $isPositive = !$type->getWithSign() || BufferUtil::isMsbZero($payload);
        if ($isPositive) {
            $value = BufferUtil::bufferToBigInt($payload);
            return new NumericalValue($type, $value);
        }

        // Also see: https://github.com/multiversx/mx-components-big-int/blob/master/twos-complement/twos2bigint.go
        BufferUtil::flipBufferBitsInPlace($payload);
        $value = BufferUtil::bufferToBigInt($payload);
        $negativeValue = $value->multipliedBy(BigInteger::of('-1'));
        $negativeValueMinusOne = $negativeValue->minus(BigInteger::of('1'));

        return new NumericalValue($type, $negativeValueMinusOne);
    }

    public function encodeNested(NumericalValue $primitive): string
    {
        if ($primitive->sizeInBytes) {
            return $this->encodeNestedFixedSize($primitive, $primitive->sizeInBytes);
        }

        // Size is not known: arbitrary-size big integer. Therefore, we must emit the length (as U32) before the actual payload.
        $buffer = $this->encodeTopLevel($primitive);
        $length = pack('N', strlen($buffer));
        return $length . $buffer;
    }

    private function encodeNestedFixedSize(NumericalValue $primitive, int $size): string
    {
        if ($primitive->valueOf()->isZero()) {
            return str_repeat("\x00", $size);
        }

        if (!$primitive->withSign) {
            $buffer = BufferUtil::bigIntToBuffer($primitive->valueOf());
            $paddingBytes = str_repeat("\x00", $size - strlen($buffer));

            return $paddingBytes . $buffer;
        }

        if ($primitive->valueOf()->isPositive()) {
            $buffer = BufferUtil::bigIntToBuffer($primitive->valueOf());

            // Fix ambiguity if any
            if (BufferUtil::isMsbOne($buffer)) {
                $buffer = BufferUtil::prependByteToBuffer($buffer, "\x00");
            }

            $paddingBytes = str_repeat("\x00", $size - strlen($buffer));
            return $paddingBytes . $buffer;
        }

        // Negative:
        // Also see: https://github.com/multiversx/mx-components-big-int/blob/master/twos-complement/bigint2twos.go
        $valuePlusOne = $primitive->valueOf()->plus(BigInteger::of('1'));
        $buffer = BufferUtil::bigIntToBuffer($valuePlusOne);
        BufferUtil::flipBufferBitsInPlace($buffer);

        // Fix ambiguity if any
        if (BufferUtil::isMsbZero($buffer)) {
            $buffer = BufferUtil::prependByteToBuffer($buffer, "\xff");
        }

        $paddingBytes = str_repeat("\xff", $size - strlen($buffer));
        return $paddingBytes . $buffer;
    }

    public function encodeTopLevel(NumericalValue $primitive): string
    {
        $withSign = $primitive->withSign;

        // Nothing or Zero:
        if ($primitive->valueOf()->isZero()) {
            return '';
        }

        // I don't care about the sign:
        if (!$withSign) {
            return BufferUtil::bigIntToBuffer($primitive->valueOf());
        }

        return $this->encodePrimitive($primitive);
    }

    private function encodePrimitive(NumericalValue $primitive): string
    {
        // Positive:
        if ($primitive->valueOf()->isPositive()) {
            $buffer = BufferUtil::bigIntToBuffer($primitive->valueOf());

            // Fix ambiguity if any
            if (BufferUtil::isMsbOne($buffer)) {
                $buffer = BufferUtil::prependByteToBuffer($buffer, "\x00");
            }

            return $buffer;
        }

        // Negative:
        // Also see: https://github.com/multiversx/mx-components-big-int/blob/master/twos-complement/bigint2twos.go
        $valuePlusOne = $primitive->valueOf()->plus(BigInteger::of('1'));
        $buffer = BufferUtil::bigIntToBuffer($valuePlusOne);
        BufferUtil::flipBufferBitsInPlace($buffer);

        // Fix ambiguity if any
        if (BufferUtil::isMsbZero($buffer)) {
            $buffer = BufferUtil::prependByteToBuffer($buffer, "\xff");
        }

        return $buffer;
    }
}
