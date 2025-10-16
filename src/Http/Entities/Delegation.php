<?php

namespace MultiversX\Http\Entities;

use MultiversX\Address;
use Brick\Math\BigInteger;
use MultiversX\Http\Api\HasApiResponses;

final class Delegation implements IEntity
{
    use HasApiResponses;

    public function __construct(
        public Address $address,
        public Address $contract,
        public BigInteger $userUnBondable,
        public BigInteger $userActiveStake,
        public BigInteger $claimableRewards,
    ) {
    }

    protected static function transformResponse(array $res): array
    {
        return array_merge($res, [
            'address' => isset($res['address']) ? Address::newFromBech32($res['address']) : Address::zero(),
            'contract' => isset($res['contract']) ? Address::newFromBech32($res['contract']) : Address::zero(),
            'userUnBondable' => isset($res['userUnBondable']) ? BigInteger::of($res['userUnBondable']) : BigInteger::zero(),
            'userActiveStake' => isset($res['userActiveStake']) ? BigInteger::of($res['userActiveStake']) : BigInteger::zero(),
            'claimableRewards' => isset($res['claimableRewards']) ? BigInteger::of($res['claimableRewards']) : BigInteger::zero(),
        ]);
    }
}
