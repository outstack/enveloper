<?php

namespace Outstack\Enveloper\Resolution;

use Outstack\Enveloper\Mail\Attachments\AttachmentList;
use Outstack\Enveloper\Templates\AttachmentListTemplate;

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
            if ($template->getIterateOver()) {
                foreach ($params[$template->getIterateOver()] as $item) {
                    $resolved[] = $this->attachmentResolver->resolve($template, ['item' => $item]);
                }
            } else {
                $resolved[] = $this->attachmentResolver->resolve($template, $params);
            }
        }

        return new AttachmentList($resolved);
    }
}