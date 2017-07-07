<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Attachments\Attachment;
use Outstack\Enveloper\Templates\AttachmentTemplate;

class AttachmentResolver
{
    /**
     * @var TemplateLanguage
     */
    private $language;

    public function __construct(TemplateLanguage $language)
    {
        $this->language = $language;
    }

    public function resolve(AttachmentTemplate $template, array $parameters)
    {
        return new Attachment(
            $this->language->render(
                $template->getContents(),
                $parameters
            ),
            $this->language->render(
                $template->getFilename(),
                $parameters
            )
        );
    }
}