<?php

namespace Outstack\Enveloper\Domain\Email\Attachments;

class AttachmentList implements \IteratorAggregate
{
    /**
     * @var Attachment[]
     */
    private $attachments = [];

    public function __construct(array $attachments)
    {
        foreach ($attachments as $attachment) {
            $this->addAttachment($attachment);
        }
    }

    private function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;
    }

    /**
     * @return \ArrayIterator|Attachment[]
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->attachments);
    }
}