<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\Domain\History\Exceptions\DeliveryAttemptNotFound;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryAttemptController extends BaseController
{

    /**
     * @Route("/outbox/{id}/delivery_attempts", name="app.delivery_attempts.list", requirements={"id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function listDeliveryAttempts(string $id)
    {
        $data = (object) [
            'items' => []
        ];

        $attempts = $this->emailDeliveryLog->findDeliveryAttempts($this->emailDeliveryLog->find($id));

        foreach ($attempts as $deliveryAttempt) {
            $data->items[] = $this->serialiseAttemptedDelivery($deliveryAttempt);
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/outbox/{id}/delivery_attempts/{index}", name="app.delivery_attempts.byIndex", requirements={"id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function findDeliveryAttempt(string $id, int $index)
    {
        try {
            $attempt = $this->emailDeliveryLog->findDeliveryAttempt($id, $index);
        } catch (DeliveryAttemptNotFound $exception) {
            return $this->problemFactory
                ->createProblem(404, 'Not Found')
                ->setDetail("Delivery attempt $index for email $id was not found")
                ->buildJsonResponse();

        }

        return new JsonResponse($this->serialiseAttemptedDelivery($attempt), 200);
    }

    /**
     * @Route("/outbox/{id}/delivery_attempts/{index}/content", name="app.delivery_attempts.view.content", requirements={"index"="\d+", "id"="[a-zA-Z0-9]{8}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{12}"}, methods={"GET"})
     */
    public function viewContentAction(Request $request, string $id, int $index)
    {
        try {
            return $this->serialiseMessageContentsNegotiatingType(
                $request,
                $this->emailDeliveryLog->findDeliveryAttempt($id, $index)->getResolvedMessage()
            );
        } catch (DeliveryAttemptNotFound $exception) {
            return $this->problemFactory
                ->createProblem(404, 'Not Found')
                ->setDetail("Delivery attempt $index for email $id was not found")
                ->buildJsonResponse();
        }
    }

}