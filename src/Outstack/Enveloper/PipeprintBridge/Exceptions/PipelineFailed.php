<?php

namespace Outstack\Enveloper\PipeprintBridge\Exceptions;

class PipelineFailed extends \Exception
{
    /**
     * @var
     */
    private $error;
    /**
     * @var
     */
    private $errorData;

    public function __construct(string $error, ?array $errorData)
    {
        $this->error = $error;
        $this->errorData = $errorData;

        $msg = "An unhandled template pipeline error occurred";
        if ($error) {
            $msg .= ": $error";
        }
        return parent::__construct($msg);
    }

    public function getError(): string
    {
        return $this->error;
    }

    public function getErrorData(): ?array
    {
        return $this->errorData;
    }
}