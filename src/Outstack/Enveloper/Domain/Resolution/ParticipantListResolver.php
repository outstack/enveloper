<?php

namespace Outstack\Enveloper\Domain\Resolution;

use Outstack\Enveloper\Domain\Email\Participants\ParticipantList;
use Outstack\Enveloper\Domain\Resolution\Templates\ParticipantListTemplate;

class ParticipantListResolver
{
    /**
     * @var ParticipantResolver
     */
    private $recipientResolver;

    public function __construct(ParticipantResolver $recipientResolver)
    {
        $this->recipientResolver = $recipientResolver;
    }


    public function resolveParticipantList(ParticipantListTemplate $template, object $parameters)
    {
        $resolvedParticipants = [];
        foreach ($template->getParticipantTemplates() as $participantTemplate) {

            $iterateOver = $participantTemplate->getIterateOver();

            if (is_null($iterateOver)) {
                $resolvedParticipants[] = $this->recipientResolver->resolveRecipient($participantTemplate, (object) $parameters);
                continue;
            }

            foreach ($parameters->$iterateOver as $item) {
                $resolvedParticipants[] = $this->recipientResolver->resolveRecipient($participantTemplate, (object) ['item' => $item]);
            }
        }

        return new ParticipantList($resolvedParticipants);
    }

}