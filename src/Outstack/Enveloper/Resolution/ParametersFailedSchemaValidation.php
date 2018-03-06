<?php

namespace Outstack\Enveloper\Resolution;

class ParametersFailedSchemaValidation extends \RuntimeException
{
    private $errors;

    public function __construct(array $validationErrors)
    {
        parent::__construct("Parameters failed schema validation");
        $this->errors = $validationErrors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}