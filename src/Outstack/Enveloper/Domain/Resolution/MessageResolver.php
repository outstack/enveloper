<?php

namespace Outstack\Enveloper\Domain\Resolution;

use Outstack\Enveloper\Domain\Email\Email;
use Outstack\Enveloper\Domain\Email\Participants\EmailAddress;
use Outstack\Enveloper\Domain\Email\Participants\Participant;
use Outstack\Enveloper\Domain\Resolution\Templates\Pipeline\TemplatePipeline;
use Outstack\Enveloper\Domain\Resolution\Templates\Template;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLanguage;

class MessageResolver
{
    /**
     * @var TemplateLanguage
     */
    private $language;
    /**
     * @var ParticipantListResolver
     */
    private $recipientListResolver;
    /**
     * @var ParticipantResolver
     */
    private $recipientResolver;
    /**
     * @var AttachmentListResolver
     */
    private $attachmentListResolver;
    /**
     * @var string
     */
    private $defaultSenderEmail;
    /**
     * @var null|string
     */
    private $defaultSenderName;
    /**
     * @var TemplatePipeline
     */
    private $pipeline;

    public function __construct(
        TemplateLanguage $language,
        TemplatePipeline $pipeline,
        ParticipantListResolver $recipientListResolver,
        ParticipantResolver $recipientResolver,
        AttachmentListResolver $attachmentListResolver,
        ?string $defaultSenderEmail,
        ?string $defaultSenderName
    ) {
        $this->language = $language;
        $this->pipeline = $pipeline;
        $this->recipientListResolver = $recipientListResolver;
        $this->recipientResolver = $recipientResolver;
        $this->defaultSenderEmail = $defaultSenderEmail;
        $this->defaultSenderName = $defaultSenderName;
        $this->attachmentListResolver = $attachmentListResolver;
    }

    public function resolve(Template $template, object $parameters): Email
    {
        $this->validate($template, $parameters);

        if (is_null($template->getSender())) {
            $from = new Participant($this->defaultSenderName, new EmailAddress($this->defaultSenderEmail));
        } else {
            $from = $this->recipientResolver->resolveRecipient($template->getSender(), $parameters);
        }

        return new Email(
            uniqid('', false),
            $this->language->render($template->getSubject(), $parameters),
            $from,
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsTo(), $parameters),
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsCc(), $parameters),
            $this->recipientListResolver->resolveParticipantList($template->getRecipientsBcc(), $parameters),
            $template->getTextTemplateName() ? $this->pipeline->render($template->getTextTemplateName(), $template->getText(), $parameters) : null,
            $this->pipeline->render($template->getHtmlTemplateName(), $template->getHtml(), $parameters),
            $this->attachmentListResolver->resolveAttachmentList($template->getAttachments(), $parameters)
        );
    }

    public function validate(Template $template, object $parameters): void
    {
        if (!is_null($template->getSchema())) {
            $dereferencer = \League\JsonReference\Dereferencer::draft6();
            $schema = $dereferencer->dereference($template->getSchema());

            $validator = new \League\JsonGuard\Validator($parameters, $schema);
            if ($validator->fails()) {
                throw new ParametersFailedSchemaValidation($validator->errors());
            }

        }
    }
}