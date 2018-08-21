<?php

namespace Outstack\Enveloper\Logging;

use Doctrine\ORM\EntityManager;

class DoctrineOrmEventLog implements EventLog
{
    /**
     * @var EntityManager
     */
    private $manager;

    public function __construct(EntityManager $manager)
    {
        $this->manager = $manager;
    }

    public function findAll()
    {
        return $this->manager->getRepository(LogEntry::class)->findAll();
    }

    public function append(LogEntry $log): void
    {
        $this->manager->persist($log);
        $this->manager->flush();
    }

    public function find(string $id): LogEntry
    {
        return $this->manager->getRepository(LogEntry::class)->find($id);
    }
}