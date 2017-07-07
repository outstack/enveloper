<?php

namespace Outstack\Enveloper\Templates;

class AttachmentListTemplate
{
    /**
     * @var AttachmentTemplate[]
     */
    private $templates = [];

    public function __construct(array $attachmentTemplates)
    {
        foreach ($attachmentTemplates as $template) {
            $this->addTemplate($template);
        }
    }

    private function addTemplate(AttachmentTemplate $template)
    {
        $this->templates[] = $template;
    }

    /**
     * @return AttachmentTemplate[]
     */
    public function getAttachmentTemplates(): array
    {
        return $this->templates;
    }
}