<?php

namespace MultiversX\Errors;

class BadMnemonicEntropyError extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
