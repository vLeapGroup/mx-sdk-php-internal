<?php

namespace MultiversX\Errors;

class SignerCannotSignError extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
