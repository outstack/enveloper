<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Outbox;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class OutboxController extends Controller
{
    /**
     * @var Outbox
     */
    private $outbox;
    /**
     * @var SentMessagesFolder
     */
    private $sentMessages;

    public function __construct(Outbox $outbox, SentMessagesFolder $sentMessages)
    {
        $this->outbox = $outbox;
        $this->sentMessages = $sentMessages;
    }

    /**
     * @Route("/outbox")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $outbox = $this->outbox;
        $payload = json_decode($request->getContent(), true);

        $outbox->send($payload['template'], $payload['parameters']);

        return new Response('', 204);
    }

    /**
     * @Route("/outbox")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $data = [];
        foreach ($this->sentMessages->listAll() as $sentMessage) {
            $resolved = $sentMessage->getResolvedMessage();
            $data[] = [
                'template' => $sentMessage->getTemplate(),
                'parameters' => $sentMessage->getParameters(),
                'resolved' => [
                    'subject' => $resolved->getSubject(),
                    'sender' => $this->serialiseParticipant($resolved->getSender()),
                    'content' => [
                        'text' => $resolved->getText(),
                        'html' => $resolved->getHtml()
                    ],
                    'recipients' => [
                        'to' => array_map([$this, 'serialiseParticipant'], $resolved->getTo()->getIterator()->getArrayCopy()),
                        'cc' => array_map([$this, 'serialiseParticipant'], $resolved->getCc()->getIterator()->getArrayCopy()),
                        'bcc' => array_map([$this, 'serialiseParticipant'], $resolved->getBcc()->getIterator()->getArrayCopy()),
                    ]
                ]
            ];
        }
        return new Response(json_encode($data), 200);
    }

    /**
     * @Route("/outbox")
     * @Method("DELETE")
     */
    public function truncateAction()
    {
        $this->sentMessages->deleteAll();
        return new Response('', 204);
    }

    private function serialiseParticipant(Participant $participant)
    {
        return [
            'name'  => $participant->isNamed()
                ? $participant->getName()
                : null,
            'email' => (string) $participant->getEmailAddress()
        ];
    }
}
