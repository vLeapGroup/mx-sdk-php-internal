<?php

namespace MultiversX\Errors;

class MissingFieldOnStructError extends \Exception
{
    public function __construct(string $fieldName, string $structName)
    {
        parent::__construct(
            sprintf('field %s does not exist on struct %s', $fieldName, $structName)
        );
    }
}
