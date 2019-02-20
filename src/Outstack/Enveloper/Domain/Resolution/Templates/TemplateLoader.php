<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates;

interface TemplateLoader
{
    public function find(string $name): Template;
}