<?php

namespace AppBundle\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\TransportInterface;

class SpoolTransport implements TransportInterface
{

    private $envelopes = [];

    /**
     * Receive some messages to the given handler.
     *
     * The handler will have, as argument, the received {@link \Symfony\Component\Messenger\Envelope} containing the message.
     * Note that this envelope can be `null` if the timeout to receive something has expired.
     */
    public function receive(callable $handler): void
    {
        foreach ($this->envelopes as $key => $envelope) {
            unset($this->envelopes[$key]);
            $handler($envelope);
        }
    }

    /**
     * Stop receiving some messages.
     */
    public function stop(): void
    {
    }

    /**
     * Sends the given envelope.
     *
     * @param Envelope $envelope
     * @return Envelope
     */
    public function send(Envelope $envelope): Envelope
    {
        $this->envelopes[] = $envelope;
        return $envelope;
    }
}