<?php

namespace Outstack\Enveloper\Mail;

class SentMessage
{
    /**
     * @var string
     */
    private $template;
    /**
     * @var array
     */
    private $parameters;
    /**
     * @var Message
     */
    private $resolvedMessage;

    private $id;
    /**
     * @var \DateTimeImmutable
     */
    private $sentAt;

    public function __construct(string $template, object $parameters, \DateTimeImmutable $sentAt, Message $resolvedMessage)
    {
        $this->template = $template;
        $this->parameters = $parameters;
        $this->sentAt = $sentAt;
        $this->resolvedMessage = $resolvedMessage;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getParameters(): object
    {
        return (object) $this->parameters;
    }

    public function getSentAt(): \DateTimeImmutable
    {
        return $this->sentAt;
    }

    public function getResolvedMessage(): Message
    {
        return $this->resolvedMessage;
    }
}