<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\ParticipantList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/outbox", name="homepage")
     */
    public function indexAction(Request $request)
    {
//        var_dump($this->getParameter('default_sender_email'));exit;
        $this->get('logger')->info('Test');
        $resolver = $this->get('enveloper.resolution.message_resolver');
        $templateLoader = $this->get('enveloper.templates.template_loader');

        $payload = json_decode($request->getContent(), true);

        $message = $resolver->resolve(
            $templateLoader->find($payload['template']),
            $payload['parameters']
        );

        $mailer = $this->get('mailer');
        $mailer->send(
            $this->convertToSwiftMessage($message)
        );

        return new Response('', 204);
    }

    private function convertToSwiftMessage(Message $message)
    {
        $swiftTo    = $this->convertToSwiftRecipientArray($message->getTo());
        $swiftCc    = $this->convertToSwiftRecipientArray($message->getCc());
        $swiftBcc   = $this->convertToSwiftRecipientArray($message->getBcc());
        $swiftFrom  = $this->convertToSwiftRecipientArray(new ParticipantList([$message->getSender()]));

        $swiftMessage = \Swift_Message::newInstance()
            ->setSubject($message->getSubject())
            ->setFrom($swiftFrom)
            ->setTo($swiftTo)
            ->setCc($swiftCc)
            ->setBcc($swiftBcc)
            ->setBody($message->getHtml(), 'text/html')
            ->addPart($message->getText(), 'text/plain');

        return $swiftMessage;

    }

    private function convertToSwiftRecipientArray(ParticipantList $recipientList)
    {
        $swiftArray = [];
        foreach ($recipientList->getIterator() as $recipient) {

            if ($recipient->isNamed()) {
                $swiftArray[(string) $recipient->getEmailAddress()] = $recipient->getName();
            } else {
                $swiftArray[] = (string) $recipient->getEmailAddress();
            }
        }

        return $swiftArray;

    }
}
