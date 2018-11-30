<?php

namespace Outstack\Enveloper\Folders;

use Outstack\Enveloper\Mail\OutboxItem;

interface SentMessagesFolder
{
    public function record(OutboxItem $resolvedMessage);

    /**
     * @return \Generator|OutboxItem[]
     */
    public function listAll();

    public function deleteAll(): void;

    public function find(string $id): OutboxItem;

}