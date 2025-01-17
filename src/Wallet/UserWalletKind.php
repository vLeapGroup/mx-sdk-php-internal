<?php

namespace MultiversX\Wallet;

enum UserWalletKind: string
{
    case SecretKey = 'secretKey';
    case Mnemonic = 'mnemonic';
}
