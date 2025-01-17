<?php

namespace MultiversX\Wallet;

use MultiversX\Address;
use MultiversX\Utils\Guards;
use SodiumException;

class UserPublicKey
{
    public const USER_PUBKEY_LENGTH = 32;

    /**
     * @throws SodiumException
     */
    public function __construct(
        private readonly string $buffer
    ) {
        Guards::guardLength($buffer, self::USER_PUBKEY_LENGTH);
    }

    /**
     * @throws SodiumException
     */
    public function verify(string $data, string $signature): bool
    {
        return sodium_crypto_sign_verify_detached(
            signature: sodium_hex2bin($signature),
            message: sodium_hex2bin($data),
            public_key: $this->buffer,
        );
    }

    public function hex(): string
    {
        return bin2hex($this->buffer);
    }

    public function toAddress(?string $hrp = null): Address
    {
        return new Address($this->buffer, $hrp);
    }

    public function valueOf(): string
    {
        return $this->buffer;
    }
}
