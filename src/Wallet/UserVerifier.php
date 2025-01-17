<?php

namespace MultiversX\Wallet;

use MultiversX\Bytes;
use MultiversX\Interfaces\IAddress;

/**
 * ed25519 signature verification
 */
class UserVerifier
{
    public function __construct(
        private readonly UserPublicKey $publicKey
    ) {
    }

    public static function fromAddress(IAddress $address): self
    {
        $publicKey = new UserPublicKey($address->getPublicKey());

        return new self($publicKey);
    }

    /**
     * @param Bytes $data the raw data to be verified (e.g. an already-serialized enveloped message)
     * @param Bytes $signature the signature to be verified
     * @return bool true if the signature is valid, false otherwise
     */
    public function verify(Bytes $data, Bytes $signature): bool
    {
        return $this->publicKey->verify($data->hex, $signature->hex);
    }
}
