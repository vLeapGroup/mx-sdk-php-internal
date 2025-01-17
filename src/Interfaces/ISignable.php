<?php

namespace MultiversX\Interfaces;


interface ISignable
{
    public function serializeForSigning(): string;

    public function applySignature(string $signature): void;
}
