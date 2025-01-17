<?php

namespace MultiversX\Interfaces;

interface IUserSecretKey
{
    public function sign(string $message): string;
}
