<?php

namespace Outstack\Enveloper\Folders;


use Outstack\Enveloper\ResolvedMessage;

interface SentMessagesFolder
{
    public function record(ResolvedMessage $resolvedMessage);

    /**
     * @return \Generator|ResolvedMessage[]
     */
    public function listAll();

}