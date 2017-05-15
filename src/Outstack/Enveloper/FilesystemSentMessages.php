<?php

namespace Outstack\Enveloper;

use League\Flysystem\Filesystem;

class FilesystemSentMessages implements SentMessages
{
    /**
     * @var Filesystem
     */
    private $outboxFilesystem;

    public function __construct(Filesystem $outboxFilesystem)
    {
        $this->outboxFilesystem = $outboxFilesystem;
    }

    public function record(ResolvedMessage $resolvedMessage)
    {
        $serialised = serialize($resolvedMessage);

        $id = $resolvedMessage->getId();

        $this->outboxFilesystem->write("$id.msg", $serialised);
    }

    /**
     * @return \Generator|ResolvedMessage[]
     */
    public function listAll()
    {
        foreach ($this->outboxFilesystem->listContents() as $file) {
            yield unserialize($this->outboxFilesystem->read($file['path']));
        }
    }
}