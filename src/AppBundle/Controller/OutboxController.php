<?php

namespace AppBundle\Controller;

use League\JsonGuard\ValidationError;
use Outstack\Components\ApiProvider\ApiProblemDetails\ApiProblemFactory;
use Outstack\Enveloper\Folders\SentMessagesFolder;
use Outstack\Enveloper\Mail\Message;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Mail\SentMessage;
use Outstack\Enveloper\Outbox;
use Outstack\Enveloper\PipeprintBridge\Exceptions\PipelineFailed;
use Outstack\Enveloper\Resolution\ParametersFailedSchemaValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OutboxController extends AbstractController
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
    /**
     * @var string
     */
    private $projectDir;

    public function __construct(Outbox $outbox, SentMessagesFolder $sentMessages, ApiProblemFactory $problemFactory, string $projectDir)
    {
        $this->outbox = $outbox;
        $this->sentMessages = $sentMessages;
        $this->problemFactory = $problemFactory;
        $this->projectDir = $projectDir;
    }

    /**
     * @Route("/outbox", methods={"POST"})
     */
    public function postAction(Request $request)
    {
        $outbox = $this->outbox;
        $payload = json_decode($request->getContent());

        $dereferencer  = \League\JsonReference\Dereferencer::draft6();
        $schema        = $dereferencer->dereference('file://' . $this->projectDir. '/schemata/endpoints/outbox/post.requestBody.schema.json');

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
        } catch (ParametersFailedSchemaValidation $e) {
            return $this->problemFactory
                ->createProblem(400, 'Parameters failed JSON schema validation')
                ->setDetail('A template was found but the parameters submitted to it do not validate against the configured JSON schema')
                ->addField('errors', array_map(
                        function(ValidationError $e) {
                            return [
                                'error' => $e->getMessage(),
                                'path' => $e->getSchemaPath()
                            ];
                        }, $e->getErrors())
                )
                ->buildJsonResponse();

        }
    }

    /**
     * @Route("/outbox/preview", methods={"POST"})
     */
    public function previewAction(Request $request)
    {
        $outbox = $this->outbox;
        $payload = json_decode($request->getContent());

        $dereferencer  = \League\JsonReference\Dereferencer::draft6();
        $schema        = $dereferencer->dereference('file://' . $this->projectDir . '/schemata/endpoints/outbox/preview/post.requestBody.schema.json');

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
            $message = $outbox->preview($payload->template, $payload->parameters);

            return $this->serialiseMessageContentsNegotiatingType($request, $message);

        } catch (PipelineFailed $e) {
            return $this->problemFactory
                ->createProblem(500, 'Pipeline failed')
                ->setDetail($e->getMessage())
                ->addField('pipeprintError', $e->getErrorData())
                ->buildJsonResponse();
        } catch (ParametersFailedSchemaValidation $e) {
            return $this->problemFactory
                ->createProblem(400, 'Parameters failed JSON schema validation')
                ->setDetail('A template was found but the parameters submitted to it do not validate against the configured JSON schema')
                ->addField('errors', array_map(
                        function(ValidationError $e) {
                            return [
                                'error' => $e->getMessage(),
                                'path' => $e->getSchemaPath()
                            ];
                        }, $e->getErrors())
                )
                ->buildJsonResponse();
        }
    }



    /**
     * @Route("/outbox", name="app.outbox.list", methods={"GET"})
     */
    public function listAction()
    {
        $data = (object) [
            'items' => []
        ];
        foreach ($this->sentMessages->listAll() as $sentMessage) {
            $data->items[] = $this->serialiseSentMessage($sentMessage);
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/outbox/{id}", name="app.outbox.view", requirements={"id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function findAction(string $id)
    {
        $data = $this->serialiseSentMessage($this->sentMessages->find($id));
        return new Response(json_encode($data), 200);
    }

    /**
     * @Route("/outbox/{id}/content", name="app.outbox.view.content", requirements={"id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function viewContentAction(Request $request, string $id)
    {
        return $this->serialiseMessageContentsNegotiatingType($request, $this->sentMessages->find($id)->getResolvedMessage());
    }

    /**
     * @Route("/outbox", methods={"DELETE"})
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

    private function serialiseSentMessage(SentMessage $sentMessage): array
    {
        $resolved = $sentMessage->getResolvedMessage();
        $messageData = [
            '@id' => $this->generateUrl(
                'app.outbox.view',
                [
                    'id' => $sentMessage->getId()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'template' => $sentMessage->getTemplate(),
            'parameters' => $sentMessage->getParameters(),
            'resolved' => [
                'subject' => $resolved->getSubject(),
                'sender' => $this->serialiseParticipant($resolved->getSender()),
                'content' => [
                    '@id' => $this->generateUrl('app.outbox.view.content', ['id' => $sentMessage->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
                    'availableContentTypes' => $this->serialiseAvailableContentTypes($sentMessage->getResolvedMessage())
                ],
                'recipients' => [
                    'to' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getTo()->getIterator()->getArrayCopy()),
                    'cc' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getCc()->getIterator()->getArrayCopy()),
                    'bcc' => array_map([$this, 'serialiseParticipant'],
                        $resolved->getBcc()->getIterator()->getArrayCopy()),
                ]
            ]
        ];
        return $messageData;
    }

    private function serialiseAvailableContentTypes(Message $message)
    {
        $availableContentTypes = [
            'application/json',
            'text/html'
        ];
        if ($message->getText()) {
            $availableContentTypes[] = 'text/plain';
        }

        return $availableContentTypes;
    }

    private function serialiseMessageContentsNegotiatingType(Request $request, Message $message)
    {
        $acceptableContentTypes = $request->getAcceptableContentTypes();
        if (empty($acceptableContentTypes)) {
            $acceptableContentTypes[] = 'application/json';
        }

        foreach ($acceptableContentTypes as $contentType) {
            if ($contentType == 'text/plain' && $message->getText()) {

                return new Response($message->getText(), 200, ['Content-type' => 'text/plain']);
            }
            if ($contentType == 'text/html') {
                return new Response($message->getHtml(), 200, ['Content-type' => 'text/html']);
            }

            if ($contentType == 'application/json') {
                return new JsonResponse(['html' => $message->getHtml(), 'text' => $message->getText()], 200, ['Content-type' => 'application/json']);
            }
        }

        $availableContentTypes = $this->serialiseAvailableContentTypes($message);

        return $this->problemFactory
            ->createProblem(406, 'Not Acceptable')
            ->setDetail('No version of this email matching your Accept header could be found')
            ->addField('availableContentTypes', $availableContentTypes)
            ->buildJsonResponse();

    }
}
