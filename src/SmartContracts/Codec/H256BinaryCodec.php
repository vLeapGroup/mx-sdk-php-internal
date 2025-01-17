<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\SmartContracts\Typesystem\H256Value;

class H256BinaryCodec implements ICodec
{
    /**
     * Reads and decodes a H256Value from a given buffer.
     *
     * @param string $buffer the input buffer
     * @return array [H256Value, int]
     */
    public function decodeNested(string $buffer): array
    {
        // We don't check the size of the buffer, we just read 32 bytes.
        $slice = substr($buffer, 0, 32);
        return [new H256Value($slice), 32];
    }

    /**
     * Reads and decodes a H256Value from a given buffer.
     *
     * @param string $buffer the input buffer
     * @return H256Value
     */
    public function decodeTopLevel(string $buffer): H256Value
    {
        [$decoded, $_length] = $this->decodeNested($buffer);
        return $decoded;
    }

    /**
     * Encodes a H256Value to a buffer.
     *
     * @param H256Value $primitive
     * @return string
     */
    public function encodeNested(H256Value $primitive): string
    {
        return $primitive->valueOf();
    }

    /**
     * Encodes a H256Value to a buffer.
     *
     * @param H256Value $primitive
     * @return string
     */
    public function encodeTopLevel(H256Value $primitive): string
    {
        return $primitive->valueOf();
    }
}
