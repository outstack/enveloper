<?php

namespace AppBundle\Controller;

use Outstack\Enveloper\Logging\EventLog;
use Outstack\Enveloper\Logging\LogEntry;
use Outstack\Enveloper\Logging\LogTypes\FailedSchemaValidationLog;
use Outstack\Enveloper\Logging\LogTypes\MessageSentLog;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LogController extends Controller
{
    /**
     * @var EventLog
     */
    private $eventLog;

    public function __construct(EventLog $eventLog)
    {
        $this->eventLog = $eventLog;
    }

    /**
     * @Route("/logs", name="app.logs.list")
     * @Method("GET")
     */
    public function listAction(Request $request)
    {
        $data = (object) [
            'items' => []
        ];
        foreach ($this->eventLog->findAll() as $sentMessage) {
            $data->items[] = $this->serialiseLogEntry($sentMessage);
        }
        return new JsonResponse($data, 200);
    }

    /**
     * @Route("/logs/{id}", name="app.logs.view")
     */
    public function viewAction(Request $request, string $id)
    {
        $log = $this->eventLog->find($id);
        $data = $this->serialiseLogEntry($log, true);
        return new JsonResponse($data, 200);
    }

    private function serialiseLogEntry(LogEntry $logEntry, bool $expanded = false)
    {
        $data = [
            '@id' => $this->generateUrl(
                'app.logs.view',
                [
                    'id' => $logEntry->getId()
                ],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
        ];
        if ($expanded) {
            switch (get_class($logEntry)) {
                case MessageSentLog::class:
                    $data['message'] = [
                        '@id' => $this->generateUrl(
                            'app.outbox.view',
                            [
                                'id' => $logEntry->getId()
                            ],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        )
                    ];
                    break;
                case FailedSchemaValidationLog::class:
                    $data['errors'] = array_map(
                        function($logError) {
                            return [
                                'error' => $logError['error'],
                                'path' => $logError['path']
                            ];
                        },
                        $logEntry->getErrors()
                    );
            }
        }
        return $data;
    }
}