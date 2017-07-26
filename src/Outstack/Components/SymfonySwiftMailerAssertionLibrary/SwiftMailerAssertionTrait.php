<?php

namespace Outstack\Components\SymfonySwiftMailerAssertionLibrary;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerRecordingDecorator;

trait SwiftMailerAssertionTrait
{
    /**
     * @var SwiftMailerRecordingDecorator
     */
    protected $mailerSpy;

    protected function assertCountSentMessages(int $expectedMessageCount)
    {
        $this->assertEquals(
            $expectedMessageCount,
            $this->mailerSpy->getMessageCount()
        );
    }

    protected function assertMessageSent(callable $matcher)
    {
        $this->assertGreaterThan(0, count($this->mailerSpy->getMessages()));
        foreach ($this->mailerSpy->getMessages() as $message) {
            if ($matcher($message)) {
                return;
            }
        }

        throw new \LogicException("No matching message found");
    }

    private function doesToIncludeEmailAddress(\Swift_Message $message, string $email): bool
    {
        return array_key_exists($email, $message->getTo());
    }
    private function messageWasFromContact(\Swift_Message $message, string $expectedEmail, ?string $name): bool
    {
        $sender = $message->getFrom();
        return
            ( is_null($name) && $expectedEmail === $sender) ||
            (!is_null($name) && is_array($sender) && array_key_exists($expectedEmail, $sender) && $sender[$expectedEmail] == $name);
    }
}