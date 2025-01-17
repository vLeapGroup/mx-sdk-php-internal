<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\Types\BigIntType;
use MultiversX\SmartContracts\Typesystem\Types\ManagedDecimalSignedType;
use MultiversX\SmartContracts\Typesystem\ManagedDecimalSignedValue;
use MultiversX\SmartContracts\Typesystem\BigIntValue;
use MultiversX\SmartContracts\Typesystem\U32Value;
use Brick\Math\BigInteger;
use MultiversX\SmartContracts\Codec\Utils\BufferUtil;
use Brick\Math\BigDecimal;

class ManagedDecimalSignedCodec implements ICodec
{
    private const SIZE_OF_U32 = 4;
    private BinaryCodec $binaryCodec;

    public function __construct(BinaryCodec $binaryCodec)
    {
        $this->binaryCodec = $binaryCodec;
    }

    public function decodeNested(string $buffer, ManagedDecimalSignedType $type): array
    {
        $length = unpack('N', $buffer)[1];
        $payload = substr($buffer, 0, $length);

        $result = $this->decodeTopLevel($payload, $type);
        return [$result, $length];
    }

    public function decodeTopLevel(string $buffer, ManagedDecimalSignedType $type): ManagedDecimalSignedValue
    {
        if (strlen($buffer) === 0) {
            return new ManagedDecimalSignedValue(new BigInteger('0'), 0);
        }

        if ($type->isVariable()) {
            $bigintSize = strlen($buffer) - self::SIZE_OF_U32;

            [$value] = $this->binaryCodec->decodeNested(substr($buffer, 0, $bigintSize), new BigIntType());
            $scale = unpack('N', substr($buffer, $bigintSize))[1];

            return new ManagedDecimalSignedValue(BigDecimal::of($value->valueOf())->dividedBy(BigDecimal::of(10)->power($scale)), $scale);
        }

        $value = BufferUtil::bufferToBigInt($buffer);
        $metadata = $type->getMetadata();
        $scale = $metadata !== "usize" ? intval($metadata) : 0;
        return new ManagedDecimalSignedValue(BigDecimal::of($value)->dividedBy(BigDecimal::of(10)->power($scale)), $scale);
    }

    public function encodeNested(ManagedDecimalSignedValue $value): string
    {
        $buffers = [];
        $rawValue = new BigIntValue(BigDecimal::of($value->valueOf())->multipliedBy(BigDecimal::of(10)->power($value->getScale())));

        if ($value->isVariable()) {
            $buffers[] = $this->binaryCodec->encodeNested($rawValue);
            $buffers[] = $this->binaryCodec->encodeNested(new U32Value($value->getScale()));
        } else {
            $buffers[] = $this->binaryCodec->encodeTopLevel($rawValue);
        }

        return implode('', $buffers);
    }

    public function encodeTopLevel(ManagedDecimalSignedValue $value): string
    {
        return $this->encodeNested($value);
    }
}
