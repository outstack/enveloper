<?php

namespace Outstack\Enveloper\Infrastructure\History\EmailDeliveryLog\DoctrineOrm;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Outstack\Enveloper\Domain\Delivery\AttemptedDelivery;
use Outstack\Enveloper\Domain\Email\EmailRequest;
use Outstack\Enveloper\Domain\History\EmailDeliveryLog;
use Outstack\Enveloper\Domain\History\Exceptions\DeliveryAttemptNotFound;
use Outstack\Enveloper\Domain\History\Exceptions\EmailRequestNotFound;
use Outstack\Enveloper\Mail\OutboxItem;

class DoctrineOrmEmailDeliveryLog implements EmailDeliveryLog
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function recordInitialRequest(EmailRequest $emailRequest)
    {
        $this->manager->persist($emailRequest);
        $this->manager->flush();
    }

    /**
     * @return \Generator|EmailRequest[]
     */
    public function listAll()
    {
        return $this->manager->getRepository(EmailRequest::class)->findAll();
    }

    public function deleteAll(): void
    {
        $this->manager->createQuery("DELETE FROM " . EmailRequest::class)->execute();
    }

    public function find(string $id): EmailRequest
    {
        $request = $this->manager->getRepository(EmailRequest::class)->find($id);
        if ($request === null) {
            throw new EmailRequestNotFound($id);
        }
        return $request;
    }

    public function recordAttemptedDelivery(AttemptedDelivery $attemptedDelivery)
    {
        $this->manager->persist($attemptedDelivery);
        $this->manager->flush();
    }

    public function findDeliveryAttempts(EmailRequest $emailRequest)
    {
        return $this->manager->getRepository(AttemptedDelivery::class)->findBy(['emailRequest' => $emailRequest]);
    }

    public function countDeliveryAttempts($emailRequest): int
    {
        return \count($this->findDeliveryAttempts($emailRequest));
    }

    /**
     * @throws DeliveryAttemptNotFound
     */
    public function findDeliveryAttempt(string $id, int $index): AttemptedDelivery
    {
        $attempt = $this->manager
            ->getRepository(AttemptedDelivery::class)
            ->findOneBy(['emailRequest' => $id, 'attemptNumber' => $index]);

        if ($attempt === null) {
            throw new DeliveryAttemptNotFound($id, $index);
        }

        return $attempt;
    }
}