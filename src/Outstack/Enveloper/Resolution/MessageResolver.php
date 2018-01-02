<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Attachments\AttachmentList;
use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\EmailAddress;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Templates\Template;
use Outstack\Enveloper\Templates\TemplatePipeline;

class MessageResolver
{
    /**
     * @var TemplateLanguage
     */
    private $language;
    /**
     * @var ParticipantListResolver
     */
    private $recipientListResolver;
    /**
     * @var ParticipantResolver
     */
    private $recipientResolver;
    /**
     * @var AttachmentListResolver
     */
    private $attachmentListResolver;
    /**
     * @var string
     */
    private $defaultSenderEmail;
    /**
     * @var null|string
     */
    private $defaultSenderName;
    /**
     * @var TemplatePipeline
     */
    private $pipeline;

    public function __construct(
        TemplateLanguage $language,
        TemplatePipeline $pipeline,
        ParticipantListResolver $recipientListResolver,
        ParticipantResolver $recipientResolver,
        AttachmentListResolver $attachmentListResolver,
        ?string $defaultSenderEmail,
        ?string $defaultSenderName
    ) {
        $this->language = $language;
        $this->pipeline = $pipeline;
        $this->recipientListResolver = $recipientListResolver;
        $this->recipientResolver = $recipientResolver;
        $this->defaultSenderEmail = $defaultSenderEmail;
        $this->defaultSenderName = $defaultSenderName;
        $this->attachmentListResolver = $attachmentListResolver;
    }

    public function resolve(Template $template, object $parameters): Message
    {
        if (is_null($template->getSender())) {
            $from = new Participant($this->defaultSenderName, new EmailAddress($this->defaultSenderEmail));
        } else {
            $from = $this->recipientResolver->resolveRecipient($template->getSender(), $parameters);
        }

        return new Message(
            uniqid('', false),
            $this->language->render($template->getSubject(), $parameters),
            $from,
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsTo(), $parameters),
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsCc(), $parameters),
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsBcc(), $parameters),
            $this->pipeline->render($template->getTextTemplateName(), $template->getText(), $parameters),
            $this->pipeline->render($template->getHtmlTemplateName(), $template->getHtml(), $parameters),
            $this->attachmentListResolver->resolveAttachmentList($template->getAttachments(), $parameters)
        );
    }
}