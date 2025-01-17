<?php

namespace MultiversX;

use Brick\Math\BigInteger;

class TokenTransfer
{
    public function __construct(
        public readonly Token $token,
        public readonly BigInteger $amount,
    ) {
    }

    public static function newFromEgldAmount(BigInteger $amount): self
    {
        $token = new Token(
            identifier: Constants::EGLD_TOKEN_ID
        );

        return new self(
            token: $token,
            amount: $amount
        );
    }

    public function isEgld(): bool
    {
        return $this->token->identifier === Constants::EGLD_TOKEN_ID;
    }

    public function isFungible(): bool
    {
        return $this->token->nonce === 0;
    }
}
