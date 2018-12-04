<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates;

class TemplateNotFound extends \Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Template named $name not found");
    }
}