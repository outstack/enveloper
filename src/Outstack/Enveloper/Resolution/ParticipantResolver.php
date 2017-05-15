<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Participants\EmailAddress;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Templates\ParticipantTemplate;

class ParticipantResolver
{
    /**
     * @var TemplateLanguage
     */
    private $language;

    public function __construct(TemplateLanguage $language)
    {
        $this->language = $language;
    }

    public function resolveRecipient(ParticipantTemplate $participantTemplate, array $parameters)
    {
        $participantName = $participantTemplate->getName();

        $resolvedName = $participantName
            ? $this->language->render($participantName, $parameters)
            : null;

        $resolvedEmail = new EmailAddress(
            $this->language->render(
                $participantTemplate->getEmailAddress(),
                $parameters
            )
        );

        return new Participant($resolvedName, $resolvedEmail);
    }
}