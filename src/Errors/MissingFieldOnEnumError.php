<?php

namespace MultiversX\Errors;

/**
 * Signals a missing field on an enum.
 */
class MissingFieldOnEnumError extends \Exception
{
    public function __construct(string $fieldName, string $enumName)
    {
        parent::__construct(
            sprintf('field %s does not exist on enum %s', $fieldName, $enumName)
        );
    }
}
