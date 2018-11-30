<?php

namespace Outstack\Enveloper\Folders;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Outstack\Enveloper\Mail\OutboxItem;

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

    public function record(OutboxItem $resolvedMessage)
    {
        $this->manager->persist($resolvedMessage);
        $this->manager->flush();
    }

    /**
     * @return \Generator|OutboxItem[]
     */
    public function listAll()
    {
        return $this->manager->getRepository(OutboxItem::class)->findAll();
    }

    public function deleteAll(): void
    {
        $this->manager->createQuery("DELETE FROM " . OutboxItem::class)->execute();
    }

    public function find(string $id): OutboxItem
    {
        return $this->manager->getRepository(OutboxItem::class)->find($id);
    }
}