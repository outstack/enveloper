<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates;

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
    /**
     * @var null|string
     */
    private $textTemplateName;
    /**
     * @var string
     */
    private $htmlTemplateName;
    /**
     * @var object
     */
    private $schema;

    public function __construct(
        ?object $schema,
        string $subject,
        ?ParticipantTemplate $sender,
        ParticipantListTemplate $recipientsTo,
        ParticipantListTemplate $recipientsCc,
        ParticipantListTemplate $recipientsBcc,
        ?string $textTemplateName,
        ?string $text,
        string $htmlTemplateName,
        string $html,
        AttachmentListTemplate $attachments
    ) {
        $this->subject = $subject;
        $this->recipientsTo = $recipientsTo;
        $this->recipientsCc = $recipientsCc;
        $this->recipientsBcc = $recipientsBcc;
        $this->text = $text;
        $this->textTemplateName = $textTemplateName;
        $this->html = $html;
        $this->htmlTemplateName = $htmlTemplateName;
        $this->sender = $sender;
        $this->attachments = $attachments;
        $this->schema = $schema;
    }

    public function getSchema(): ?object
    {
        return $this->schema;
    }

    /**
     * @return null|string
     */
    public function getTextTemplateName()
    {
        return $this->textTemplateName;
    }

    /**
     * @return string
     */
    public function getHtmlTemplateName(): string
    {
        return $this->htmlTemplateName;
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