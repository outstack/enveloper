<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Attachments\AttachmentList;
use Outstack\Enveloper\Templates\AttachmentListTemplate;
use Outstack\Enveloper\Templates\AttachmentTemplate;

class AttachmentListResolver
{
    /**
     * @var AttachmentResolver
     */
    private $attachmentResolver;

    public function __construct(AttachmentResolver $attachmentResolver)
    {
        $this->attachmentResolver = $attachmentResolver;
    }

    public function resolveAttachmentList(AttachmentListTemplate $attachmentListTemplate, object $parameters): AttachmentList
    {
        $resolved = [];
        foreach ($attachmentListTemplate->getAttachmentTemplates() as $template) {
            foreach ($this->resolveTemplate($template, $parameters) as $attachment) {
                $resolved[] = $attachment;
            }
        }

        return new AttachmentList($resolved);
    }

    private function resolveTemplate(AttachmentTemplate $template, object $parameters)
    {
        if ($template->getIterateOver()) {
            foreach ($this->resolveIteratively($template, $parameters) as $template) {
                yield $template;
            }

            return;
        }

        yield $this->attachmentResolver->resolve($template, $parameters);
    }

    private function resolveIteratively(AttachmentTemplate $template, object $parameters)
    {
        foreach ($parameters->{$template->getIterateOver()} as $item) {
            yield $this->attachmentResolver->resolve($template, (object) ['item' => $item]);
        }
    }
}