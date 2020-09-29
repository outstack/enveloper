<?php

namespace AppBundle\Messenger;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\TransportInterface;

class SpoolTransport implements TransportInterface
{

    private $envelopes = [];

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

    public function get(): iterable
    {
        return $this->envelopes;
    }

    public function ack(Envelope $envelope): void
    {
        $this->removeEnvelopeFromQueue($envelope);
    }

    public function reject(Envelope $envelope): void
    {
        $this->removeEnvelopeFromQueue($envelope);
    }

    private function removeEnvelopeFromQueue(Envelope $envelope): void
    {
        foreach ($this->envelopes as $key => $queued) {
            if ($queued->getMessage() === $envelope->getMessage()) {
                unset($this->envelopes[$key]);
            }
        }
    }
}
