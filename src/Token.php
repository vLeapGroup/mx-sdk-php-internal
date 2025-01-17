<?php

namespace MultiversX;

class Token
{
    public function __construct(
        public readonly string $identifier,
        public readonly int $nonce = 0,
    ) {
    }
}
