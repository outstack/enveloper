<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\EmailAddress;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Templates\Template;

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
     * @var string
     */
    private $defaultSenderEmail;
    /**
     * @var null|string
     */
    private $defaultSenderName;

    public function __construct(TemplateLanguage $language, ParticipantListResolver $recipientListResolver, ParticipantResolver $recipientResolver, ?string $defaultSenderEmail, ?string $defaultSenderName)
    {
        $this->language = $language;
        $this->recipientListResolver = $recipientListResolver;
        $this->recipientResolver = $recipientResolver;
        $this->defaultSenderEmail = $defaultSenderEmail;
        $this->defaultSenderName = $defaultSenderName;
    }

    public function resolve(Template $template, $parameters): Message
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
            $this->language->render($template->getText(), $parameters),
            $this->language->render($template->getHtml(), $parameters)
        );
    }
}