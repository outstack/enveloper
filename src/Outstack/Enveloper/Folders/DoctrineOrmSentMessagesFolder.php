<?php

namespace Outstack\Enveloper\Folders;

use Doctrine\ORM\EntityManager;
use Outstack\Enveloper\Mail\SentMessage;

class DoctrineOrmSentMessagesFolder implements SentMessagesFolder
{
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
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
}