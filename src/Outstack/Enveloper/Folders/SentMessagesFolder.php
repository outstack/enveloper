<?php

namespace Outstack\Enveloper\Folders;

use Outstack\Enveloper\Mail\SentMessage;

interface SentMessagesFolder
{
    public function record(SentMessage $resolvedMessage);

    /**
     * @return \Generator|SentMessage[]
     */
    public function listAll();

    public function deleteAll(): void;

}