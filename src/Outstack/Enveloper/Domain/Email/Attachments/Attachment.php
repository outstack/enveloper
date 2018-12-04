<?php

namespace Outstack\Enveloper\Domain\Email\Attachments;

class Attachment
{
    /**
     * @var string
     */
    private $data;
    /**
     * @var string
     */
    private $filename;

    public function __construct(string $data, string $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}