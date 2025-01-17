<?php

use Brick\Math\BigInteger;
use MultiversX\TokenTransfer;
use MultiversX\Token;
use MultiversX\Constants;

describe('TokenTransfer', function () {
    it('should work with EGLD', function () {
        $transfer = TokenTransfer::newFromEgldAmount(BigInteger::of("1000000000000000000"));

        expect($transfer->token->identifier)->toBe(Constants::EGLD_TOKEN_ID)
            ->and($transfer->token->nonce)->toBe(0)
            ->and($transfer->amount)->toEqual(BigInteger::of("1000000000000000000"))
            ->and($transfer->isEgld())->toBeTrue()
            ->and($transfer->isFungible())->toBeTrue();
    });

    it('should work with fungible tokens', function () {
        $token = new Token(
            identifier: 'USDC-c76f1f',
            nonce: 0
        );

        $transfer = new TokenTransfer(
            token: $token,
            amount: BigInteger::of("1000000")
        );

        expect($transfer->token->identifier)->toBe('USDC-c76f1f')
            ->and($transfer->token->nonce)->toBe(0)
            ->and($transfer->amount)->toEqual(BigInteger::of("1000000"))
            ->and($transfer->isEgld())->toBeFalse()
            ->and($transfer->isFungible())->toBeTrue();
    });

    it('should work with non-fungible tokens', function () {
        $token = new Token(
            identifier: 'TEST-38f249',
            nonce: 1
        );

        $transfer = new TokenTransfer(
            token: $token,
            amount: BigInteger::one()
        );

        expect($transfer->token->identifier)->toBe('TEST-38f249')
            ->and($transfer->token->nonce)->toBe(1)
            ->and($transfer->amount)->toEqual(BigInteger::one())
            ->and($transfer->isEgld())->toBeFalse()
            ->and($transfer->isFungible())->toBeFalse();
    });

    it('should work with meta ESDT tokens', function () {
        $token = new Token(
            identifier: 'MEXFARML-28d646',
            nonce: 12345678
        );

        $transfer = new TokenTransfer(
            token: $token,
            amount: BigInteger::of("100000000000000000")
        );

        expect($transfer->token->identifier)->toBe('MEXFARML-28d646')
            ->and($transfer->token->nonce)->toBe(12345678)
            ->and($transfer->amount)->toEqual(BigInteger::of("100000000000000000"))
            ->and($transfer->isEgld())->toBeFalse()
            ->and($transfer->isFungible())->toBeFalse();
    });
});
