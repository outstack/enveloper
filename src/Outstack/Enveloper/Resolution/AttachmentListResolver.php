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

    public function resolveAttachmentList(AttachmentListTemplate $attachmentListTemplate, array $params): AttachmentList
    {
        $resolved = [];
        foreach ($attachmentListTemplate->getAttachmentTemplates() as $template) {
            foreach ($this->resolveTemplate($template, $params) as $attachment) {
                $resolved[] = $attachment;
            }
        }

        return new AttachmentList($resolved);
    }

    private function resolveTemplate(AttachmentTemplate $template, array $params)
    {
        if ($template->getIterateOver()) {
            foreach ($this->resolveIteratively($template, $params) as $template) {
                yield $template;
            }

            return;
        }

        yield $this->attachmentResolver->resolve($template, $params);
    }

    private function resolveIteratively(AttachmentTemplate $template, array $params)
    {
        foreach ($params[$template->getIterateOver()] as $item) {
            yield $this->attachmentResolver->resolve($template, ['item' => $item]);
        }
    }
}