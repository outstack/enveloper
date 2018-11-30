<?php

namespace AppBundle\Messenger;

use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Worker;

class SpoolTransportEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $receiverLocator;
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var string
     */
    private $enveloperQueueDsn;

    public function __construct(ContainerInterface $receiverLocator, MessageBusInterface $bus, string $enveloperQueueDsn)
    {
        $this->receiverLocator = $receiverLocator;
        $this->bus = $bus;
        $this->enveloperQueueDsn = $enveloperQueueDsn;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => 'onKernelTerminate'
        ];
    }

    public function onKernelTerminate(PostResponseEvent $event)
    {
        if ($this->enveloperQueueDsn === 'spool://memory') {
            $worker = new Worker($this->receiverLocator->get('email_queue'), $this->bus);
            $worker->run();
        }
    }
}