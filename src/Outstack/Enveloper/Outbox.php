<?php

namespace Outstack\Enveloper;

use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Templates\Template;

class Outbox
{
    /**
     * @var SentMessagesFolder
     */
    private $sentMessages;
    /**
     * @var MessageResolver
     */
    private $messageResolver;

    public function __construct(MessageResolver $messageResolver, SentMessagesFolder $sentMessages)
    {
        $this->sentMessages = $sentMessages;
        $this->messageResolver = $messageResolver;
    }

    public function queue(Template $template, array $parameters)
    {
        $this->sentMessages->record(
            $this->messageResolver->resolve($template, $parameters)
        );
    }
}