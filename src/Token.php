<?php

namespace MultiversX;

use Brick\Math\BigInteger;

class Token
{
    public function __construct(
        public string $identifier,
        public ?BigInteger $nonce = null,
    ) {
        $this->nonce = $nonce ?? BigInteger::zero();
    }
}
