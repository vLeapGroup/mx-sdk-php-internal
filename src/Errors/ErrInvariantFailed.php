<?php

namespace MultiversX\Errors;

class ErrInvariantFailed extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
