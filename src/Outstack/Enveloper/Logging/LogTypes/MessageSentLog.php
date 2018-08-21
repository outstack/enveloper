<?php

namespace Outstack\Enveloper\Logging\LogTypes;

use Outstack\Enveloper\Logging\LogEntry;

class MessageSentLog extends LogEntry
{
    /**
     * @var string
     */
    private $messageId;

    public function __construct(\DateTimeImmutable $logDate, string $messageId)
    {
        $this->logDate = $logDate;
        $this->messageId = $messageId;
    }
}