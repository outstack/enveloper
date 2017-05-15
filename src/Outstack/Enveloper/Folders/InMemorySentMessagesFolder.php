<?php

namespace Outstack\Enveloper\Folders;

use Outstack\Enveloper\ResolvedMessage;

class InMemorySentMessagesFolder implements SentMessagesFolder
{
    private $sentMessages = [];

    public function record(ResolvedMessage $resolvedMessage)
    {
        $this->sentMessages[] = $resolvedMessage;
    }

    /**
     * @return \Generator|ResolvedMessage[]
     */
    public function listAll()
    {
        foreach ($this->sentMessages as $message) {
            yield $message;
        }
    }
}