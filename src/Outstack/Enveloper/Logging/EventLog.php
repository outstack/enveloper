<?php

namespace Outstack\Enveloper\Logging;

interface EventLog
{
    public function find(string $id): LogEntry;
    public function findAll();
    public function append(LogEntry $log): void;
}