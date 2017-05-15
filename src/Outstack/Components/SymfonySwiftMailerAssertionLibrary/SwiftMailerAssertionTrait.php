<?php

namespace Outstack\Components\SymfonySwiftMailerAssertionLibrary;

use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;

trait SwiftMailerAssertionTrait
{
    /**
     * @var MessageDataCollector
     */
    private $messageDataCollector;

    protected function getMessageDataCollector(): MessageDataCollector
    {
        if (is_null($this->messageDataCollector)) {
            throw new \LogicException("Cannot assert on messages sent without calling setMessageDataCollector first");
        }
        return $this->messageDataCollector;
    }

    public function setMessageDataCollector(MessageDataCollector $messageDataCollector)
    {
        $this->messageDataCollector = $messageDataCollector;
    }

    protected function assertCountSentMessages(int $expectedMessageCount)
    {
        $this->assertEquals(
            $expectedMessageCount,
            $this->getMessageDataCollector()->getMessageCount()
        );
    }

    protected function assertMessageSent(callable $matcher)
    {
        $collector = $this->getMessageDataCollector();

        $this->assertGreaterThan(0, count($collector->getMessages()));
        foreach ($collector->getMessages() as $message) {
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
    private function messageWasFromEmailAddress(\Swift_Message $message, string $email): bool
    {
        $sender = $message->getFrom();
        return $email === $sender || is_array($sender) && array_key_exists($email, $sender);
    }
}