<?php

namespace MultiversX\Interfaces;

interface IUserPublicKey
{
    public function toAddress(?string $hrp = null): object;
}
