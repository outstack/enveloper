<?php

namespace Outstack\Enveloper\Domain\Email;

use Outstack\Enveloper\Domain\Email\Attachments\AttachmentList;
use Outstack\Enveloper\Domain\Email\Participants\Participant;
use Outstack\Enveloper\Domain\Email\Participants\ParticipantList;

class Email
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $subject;
    /**
     * @var ?string
     */
    private $text;

    /**
     * @var ParticipantList
     */
    private $to;
    /**
     * @var ParticipantList
     */
    private $cc;
    /**
     * @var ParticipantList
     */
    private $bcc;
    /**
     * @var string
     */
    private $html;
    /**
     * @var Participant
     */
    private $sender;
    /**
     * @var AttachmentList
     */
    private $attachments;

    public function __construct(
        string $id,
        string $subject,
        Participant $sender,
        ParticipantList $to,
        ParticipantList $cc,
        ParticipantList $bcc,
        ?string $text,
        string $html,
        AttachmentList $attachments
    )
    {
        $this->id = $id;
        $this->subject = $subject;
        $this->sender = $sender;
        $this->text = $text;
        $this->to = $to;
        $this->cc = $cc;
        $this->bcc = $bcc;
        $this->html = $html;
        $this->attachments = $attachments;
    }

    /**
     * @return ParticipantList|Participant[]
     */
    public function getTo(): ParticipantList
    {
        return $this->to;
    }

    /**
     * @return ParticipantList|Participant[]
     */
    public function getCc(): ParticipantList
    {
        return $this->cc;
    }

    /**
     * @return ParticipantList|Participant[]
     */
    public function getBcc(): ParticipantList
    {
        return $this->bcc;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getSender(): Participant
    {
        return $this->sender;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getAttachments(): AttachmentList
    {
        return $this->attachments;
    }
}