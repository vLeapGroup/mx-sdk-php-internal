<?php

namespace MultiversX\Errors;

/**
 * Signals a generic codec (encode / decode) error.
 */
class ErrCodec extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
