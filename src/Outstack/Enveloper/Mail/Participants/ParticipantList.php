<?php

namespace Outstack\Enveloper\Mail\Participants;

class ParticipantList implements \IteratorAggregate
{
    /**
     * @var Participant[]
     */
    private $recipients = [];

    public function __construct(array $recipients)
    {
        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    private function addRecipient(Participant $recipient)
    {
        $this->recipients[] = $recipient;
    }

    /**
     * @return \ArrayIterator|Participant[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->recipients);
    }
}