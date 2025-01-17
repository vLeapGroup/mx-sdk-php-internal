<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\Address;
use MultiversX\SmartContracts\Typesystem\AddressValue;

class AddressBinaryCodec implements ICodec
{
    /**
     * Reads and decodes an AddressValue from a given buffer.
     *
     * @param string $buffer the input buffer
     * @return array [AddressValue, int]
     */
    public function decodeNested(string $buffer): array
    {
        // We don't check the size of the buffer, we just read 32 bytes.
        $slice = substr($buffer, 0, 32);
        $value = new Address($slice);
        return [new AddressValue($value), 32];
    }

    /**
     * Reads and decodes an AddressValue from a given buffer.
     *
     * @param string $buffer the input buffer
     * @return AddressValue
     */
    public function decodeTopLevel(string $buffer): AddressValue
    {
        [$decoded, $_length] = $this->decodeNested($buffer);
        return $decoded;
    }

    /**
     * Encodes an AddressValue to a buffer.
     *
     * @param AddressValue $primitive
     * @return string
     */
    public function encodeNested(AddressValue $primitive): string
    {
        return $primitive->valueOf()->getPublicKey();
    }

    /**
     * Encodes an AddressValue to a buffer.
     *
     * @param AddressValue $primitive
     * @return string
     */
    public function encodeTopLevel(AddressValue $primitive): string
    {
        return $primitive->valueOf()->getPublicKey();
    }
}
