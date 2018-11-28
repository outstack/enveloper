<?php

namespace Outstack\Enveloper\Folders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Outstack\Enveloper\Mail\SentMessage;

class DoctrineOrmSentMessagesFolder implements SentMessagesFolder
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public function record(SentMessage $resolvedMessage)
    {
        $this->manager->persist($resolvedMessage);
        $this->manager->flush();
    }

    /**
     * @return \Generator|SentMessage[]
     */
    public function listAll()
    {
        return $this->manager->getRepository(SentMessage::class)->findAll();
    }

    public function deleteAll(): void
    {
        $this->manager->createQuery("DELETE FROM " . SentMessage::class)->execute();
    }

    public function find(string $id): SentMessage
    {
        return $this->manager->getRepository(SentMessage::class)->find($id);
    }
}