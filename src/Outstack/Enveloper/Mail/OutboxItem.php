<?php

namespace Outstack\Enveloper\Mail;

class OutboxItem
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

    public function __construct(string $template, object $parameters, ?Message $resolvedMessage)
    {
        $this->template = $template;
        $this->parameters = $parameters;
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

    public function setResolvedMessage(Message $message)
    {
        $this->resolvedMessage = $message;
    }

    public function getResolvedMessage(): Message
    {
        if ($this->resolvedMessage === null) {
            throw new \LogicException('Message has not yet been resolved');
        }
        return $this->resolvedMessage;
    }
}