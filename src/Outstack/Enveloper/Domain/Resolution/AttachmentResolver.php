<?php

namespace Outstack\Enveloper\Domain\Resolution;


use Outstack\Enveloper\Domain\Email\Attachments\Attachment;
use Outstack\Enveloper\Domain\Resolution\Templates\AttachmentTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLanguage;

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

    public function resolve(AttachmentTemplate $template, object $parameters)
    {
        return new Attachment(
            $template->isStatic()
                ? $template->getContents()
                : $this->language->render($template->getContents(), $parameters),
            $this->language->render(
                $template->getFilename(),
                $parameters
            )
        );
    }
}