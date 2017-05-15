<?php

namespace Outstack\Enveloper\Templates;

class ParticipantListTemplate
{
    /**
     * @var ParticipantTemplate[]
     */
    private $templates = [];

    public function __construct(array $recipientTemplates)
    {
        foreach ($recipientTemplates as $template) {
            $this->addTemplate($template);
        }
    }

    private function addTemplate(ParticipantTemplate $template)
    {
        $this->templates[] = $template;
    }

    /**
     * @return ParticipantTemplate[]
     */
    public function getParticipantTemplates(): array
    {
        return $this->templates;
    }
}