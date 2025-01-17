<?php

namespace MultiversX\SmartContracts\Codec;

use Brick\Math\BigDecimal;
use MultiversX\SmartContracts\Typesystem\Types\BigUIntType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalType;
use MultiversX\SmartContracts\Typesystem\ManagedDecimalValue;
use MultiversX\SmartContracts\Typesystem\BigUIntValue;
use MultiversX\SmartContracts\Typesystem\U32Value;
use MultiversX\SmartContracts\Codec\Utils\BufferUtil;

class ManagedDecimalCodec implements ICodec
{
    private const SIZE_OF_U32 = 4;
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    public function decodeNested(string $buffer, ManagedDecimalType $type): array
    {
        $length = unpack('N', $buffer)[1];
        $payload = substr($buffer, 0, $length);

        $result = $this->decodeTopLevel($payload, $type);
        return [$result, $length];
    }

    public function decodeTopLevel(string $buffer, ManagedDecimalType $type): ManagedDecimalValue
    {
        if (strlen($buffer) === 0) {
            return new ManagedDecimalValue(new BigDecimal('0'), 0);
        }

        if ($type->isVariable()) {
            $bigUintSize = strlen($buffer) - self::SIZE_OF_U32;

            [$value] = $this->binaryCodec->decodeNested(substr($buffer, 0, $bigUintSize), new BigUIntType());
            $scale = unpack('N', substr($buffer, $bigUintSize))[1];
            return new ManagedDecimalValue(BigDecimal::of($value->valueOf())->dividedBy(BigDecimal::of(10)->power($scale)), $scale);
        }

        $value = BufferUtil::bufferToBigInt($buffer);
        $metadata = $type->getMetadata();
        $scale = $metadata !== "usize" ? intval($metadata) : 0;
        return new ManagedDecimalValue(BigDecimal::of($value)->dividedBy(BigDecimal::of(10)->power($scale)), $scale);
    }

    public function encodeNested(ManagedDecimalValue $value): string
    {
        $buffers = [];
        $rawValue = new BigUIntValue(BigDecimal::of($value->valueOf())->multipliedBy(BigDecimal::of(10)->power($value->getScale())));

        if ($value->isVariable()) {
            $buffers[] = $this->binaryCodec->encodeNested($rawValue);
            $buffers[] = $this->binaryCodec->encodeNested(new U32Value($value->getScale()));
        } else {
            $buffers[] = $this->binaryCodec->encodeTopLevel($rawValue);
        }

        return implode('', $buffers);
    }

    public function encodeTopLevel(ManagedDecimalValue $value): string
    {
        return $this->encodeNested($value);
    }
}
