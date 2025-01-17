<?php

namespace MultiversX\SmartContracts\Typesystem;

class EndpointModifiers
{
    /**
     * @param string $mutability
     * @param string[] $payableInTokens
     * @param bool $onlyOwner
     */
    public function __construct(
        public readonly string $mutability = '',
        public readonly array $payableInTokens = [],
        public readonly bool $onlyOwner = false
    ) {
    }

    public function isPayableInEGLD(): bool
    {
        return $this->isPayableInToken('EGLD');
    }

    public function isPayableInToken(string $token): bool
    {
        if (in_array($token, $this->payableInTokens, true)) {
            return true;
        }

        if (in_array("!{$token}", $this->payableInTokens, true)) {
            return false;
        }

        if (in_array('*', $this->payableInTokens, true)) {
            return true;
        }

        return false;
    }

    public function isPayable(): bool
    {
        return count($this->payableInTokens) !== 0;
    }

    public function isReadonly(): bool
    {
        return $this->mutability === 'readonly';
    }

    public function isOnlyOwner(): bool
    {
        return $this->onlyOwner;
    }
}
