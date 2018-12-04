<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates;

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
    /**
     * @var bool
     */
    private $static;

    public function __construct(bool $static, string $contents, string $filename, ?string $iterateOver = null)
    {
        $this->contents = $contents;
        $this->filename = $filename;
        $this->iterateOver = $iterateOver;
        $this->static = $static;
    }

    public function isStatic(): bool
    {
        return $this->static;
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