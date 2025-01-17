<?php

namespace MultiversX\Wallet;

use MultiversX\Address;
use MultiversX\Errors\SignerCannotSignError;
use MultiversX\Interfaces\IUserSecretKey;
use MultiversX\Wallet\UserSecretKey;

/**
 * ed25519 signer
 */
class UserSigner
{
    public function __construct(
        protected readonly IUserSecretKey $secretKey
    ) {
    }

    public static function fromPem(string $text, int $index = 0): self
    {
        $secretKey = UserSecretKey::fromPem($text, $index);
        return new self($secretKey);
    }

    /**
     * @throws SignerCannotSignError
     */
    public function sign(string $data): string
    {
        try {
            return $this->secretKey->sign($data);
        } catch (\Exception $err) {
            throw new SignerCannotSignError($err->getMessage());
        }
    }
}
