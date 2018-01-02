<?php

namespace AppBundle\Controller;

use League\JsonGuard\ValidationError;
use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Outbox;
use Outstack\Enveloper\PipeprintBridge\Exceptions\PipelineFailed;
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
    /**
     * @var ApiProblemFactory
     */
    private $problemFactory;

    public function __construct(Outbox $outbox, SentMessagesFolder $sentMessages, ApiProblemFactory $problemFactory)
    {
        $this->outbox = $outbox;
        $this->sentMessages = $sentMessages;
        $this->problemFactory = $problemFactory;
    }

    /**
     * @Route("/outbox")
     * @Method("POST")
     */
    public function postAction(Request $request)
    {
        $outbox = $this->outbox;
        $payload = json_decode($request->getContent());

        $dereferencer  = \League\JsonReference\Dereferencer::draft4();
        $schema        = $dereferencer->dereference('file://' . $this->container->getParameter('kernel.root_dir'). '/../schemata/outbox_post.json');

        $validator     = new \League\JsonGuard\Validator($payload, $schema);

        if ($validator->fails()) {
            return $this->problemFactory
                ->createProblem(400, 'Syntax Error')
                ->setDetail('Request failed JSON schema validation')
                ->addField('errors', array_map(
                    function(ValidationError $e) {
                        return [
                            'error' => $e->getMessage(),
                            'path' => $e->getSchemaPath()
                        ];
                    }, $validator->errors())
                )
                ->buildJsonResponse();
        }

        try {
            $outbox->send($payload->template, $payload->parameters);
            return new Response('', 204);
        } catch (PipelineFailed $e) {
            return $this->problemFactory
                ->createProblem(500, 'Pipeline failed')
                ->setDetail($e->getMessage())
                ->addField('pipeprintError', $e->getErrorData())
                ->buildJsonResponse();
        }
    }

    /**
     * @Route("/outbox")
     * @Method("GET")
     */
    public function listAction()
    {
        $data = [];
        foreach ($this->sentMessages->listAll() as $sentMessage) {
            $resolved = $sentMessage->getResolvedMessage();
            $data[] = [
                'id' => $sentMessage->getId(),
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
