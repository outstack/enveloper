<?php

namespace Outstack\Enveloper\Templates;

class AttachmentTemplate
{
    /**
     * @var string
     */
    private $contents;
    /**
     * @var string
     */
    private $filename;
    /**
     * @var null|string
     */
    private $iterateOver;

    public function __construct(string $contents, string $filename, ?string $iterateOver = null)
    {
        $this->contents = $contents;
        $this->filename = $filename;
        $this->iterateOver = $iterateOver;
    }

    public function getContents(): string
    {
        return $this->contents;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getIterateOver(): ?string
    {
        return $this->iterateOver;
    }
}