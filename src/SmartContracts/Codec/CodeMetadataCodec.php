<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\CodeMetadata;
use MultiversX\SmartContracts\Typesystem\CodeMetadataValue;

class CodeMetadataCodec implements ICodec
{
    private const CODE_METADATA_LENGTH = 2;

    /**
     * @param string $buffer
     * @return array [CodeMetadataValue, int]
     */
    public function decodeNested(string $buffer): array
    {
        $codeMetadata = CodeMetadata::fromBuffer(substr($buffer, 0, self::CODE_METADATA_LENGTH));
        return [new CodeMetadataValue($codeMetadata), self::CODE_METADATA_LENGTH];
    }

    public function decodeTopLevel(string $buffer): CodeMetadataValue
    {
        $codeMetadata = CodeMetadata::fromBuffer($buffer);
        return new CodeMetadataValue($codeMetadata);
    }

    public function encodeNested(CodeMetadataValue $codeMetadata): string
    {
        return $codeMetadata->valueOf()->toBuffer();
    }

    public function encodeTopLevel(CodeMetadataValue $codeMetadata): string
    {
        return $codeMetadata->valueOf()->toBuffer();
    }
}
