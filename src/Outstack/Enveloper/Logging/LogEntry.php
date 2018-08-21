<?php

namespace Outstack\Enveloper\Logging;

abstract class LogEntry
{
    protected $id;
    protected $type;
    /**
     * @var \DateTimeImmutable
     */
    protected $logDate;

    public function getId(): string
    {
        return $this->id;
    }
}
