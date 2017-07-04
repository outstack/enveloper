<?php

namespace Outstack\Enveloper\Templates;

use Outstack\Enveloper\Mail\Participants\Participant;

class Template
{
    /**
     * @var string
     */
    private $subject;
    /**
     * @var array
     */
    private $recipientsTo;
    /**
     * @var array
     */
    private $recipientsCc;
    /**
     * @var array
     */
    private $recipientsBcc;
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $html;
    /**
     * @var ParticipantTemplate
     */
    private $sender;
    /**
     * @var AttachmentListTemplate
     */
    private $attachments;

    public function __construct(
        string $subject,
        ?ParticipantTemplate $sender,
        ParticipantListTemplate $recipientsTo,
        ParticipantListTemplate $recipientsCc,
        ParticipantListTemplate $recipientsBcc,
        ?string $text,
        string $html,
        AttachmentListTemplate $attachments
    ) {
        $this->subject = $subject;
        $this->recipientsTo = $recipientsTo;
        $this->recipientsCc = $recipientsCc;
        $this->recipientsBcc = $recipientsBcc;
        $this->text = $text;
        $this->html = $html;
        $this->sender = $sender;
        $this->attachments = $attachments;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getSender(): ?ParticipantTemplate
    {
        return $this->sender;
    }

    public function getRecipientsTo(): ParticipantListTemplate
    {
        return $this->recipientsTo;
    }

    public function getRecipientsCc(): ParticipantListTemplate
    {
        return $this->recipientsCc;
    }

    public function getRecipientsBcc(): ParticipantListTemplate
    {
        return $this->recipientsBcc;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getHtml(): string
    {
        return $this->html;
    }

    public function getAttachments(): AttachmentListTemplate
    {
        return $this->attachments;
    }
}