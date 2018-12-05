<?php

namespace AppBundle\Controller;

use League\JsonGuard\ValidationError;
use Outstack\Enveloper\Application\PreviewEmail;
use Outstack\Enveloper\Application\QueueEmailRequest;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\History\Exceptions\EmailRequestNotFound;
use Outstack\Enveloper\Domain\Resolution\ParametersFailedSchemaValidation;
use Outstack\Enveloper\Domain\Resolution\Templates\Pipeline\Exceptions\PipelineFailed;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OutboxController extends BaseController
{
    /**
     * @Route("/outbox", methods={"POST"})
     */
    public function postAction(Request $request, QueueEmailRequest $queueEmailRequest)
    {
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
            $queueEmailRequest(
                new EmailRequest($payload->template, $payload->parameters, new \DateTimeImmutable('now'))
            );
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
    public function previewAction(Request $request, PreviewEmail $previewEmail)
    {
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
            $message = $previewEmail($payload->template, $payload->parameters);

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
        foreach ($this->emailDeliveryLog->listAll() as $sentMessage) {
            $data->items[] = $this->serialiseSentMessage($sentMessage);
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/outbox/{id}", name="app.outbox.view", requirements={"id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function findAction(string $id)
    {
        try {
            $data = $this->serialiseSentMessage($this->emailDeliveryLog->find($id));
        } catch (EmailRequestNotFound $exception) {
            return $this->problemFactory
                ->createProblem(404, 'Not Found')
                ->setDetail("No email request with id $id was found")
                ->buildJsonResponse();

        }
        return new Response(json_encode($data), 200);
    }

    /**
     * @Route("/outbox", methods={"DELETE"})
     */
    public function truncateAction()
    {
        $this->emailDeliveryLog->deleteAll();
        return new Response('', 204);
    }

}
