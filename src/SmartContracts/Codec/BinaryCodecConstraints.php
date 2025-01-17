<?php

namespace MultiversX\SmartContracts\Codec;

use MultiversX\Errors\ErrCodec;

class BinaryCodecConstraints
{
    private int $maxBufferLength;
    private int $maxListLength;

    public function __construct(?array $init = null)
    {
        $this->maxBufferLength = $init['maxBufferLength'] ?? 256000;
        $this->maxListLength = $init['maxListLength'] ?? 128000;
    }

    public function checkBufferLength(string $buffer): void
    {
        if (strlen($buffer) > $this->maxBufferLength) {
            throw new ErrCodec("Buffer too large: " . strlen($buffer) . " > " . $this->maxBufferLength);
        }
    }

    /**
     * This constraint avoids computer-freezing decode bugs (e.g. due to invalid ABI or struct definitions).
     */
    public function checkListLength(int $length): void
    {
        if ($length > $this->maxListLength) {
            throw new ErrCodec("List too large: " . $length . " > " . $this->maxListLength);
        }
    }
}
