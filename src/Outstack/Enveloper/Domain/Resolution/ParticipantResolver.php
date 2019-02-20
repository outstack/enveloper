<?php

namespace Outstack\Enveloper\Domain\Resolution;

use Outstack\Enveloper\Domain\Email\Participants\EmailAddress;
use Outstack\Enveloper\Domain\Resolution\Templates\ParticipantTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLanguage;
use Outstack\Enveloper\Domain\Email\Participants\Participant;

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

    public function resolveRecipient(ParticipantTemplate $participantTemplate, object $parameters)
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