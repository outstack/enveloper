<?php

namespace Outstack\Enveloper\Logging\LogTypes;

use Outstack\Enveloper\Logging\LogEntry;

class FailedSchemaValidationLog extends LogEntry
{
    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $errors;

    public function __construct(\DateTimeImmutable $logDate, string $template, array $errors)
    {
        $this->logDate = $logDate;
        $this->template = $template;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}