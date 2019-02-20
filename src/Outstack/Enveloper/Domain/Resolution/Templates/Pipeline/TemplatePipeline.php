<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates\Pipeline;

interface TemplatePipeline
{
    public function render(string $templateName, string $templateContents, object $parameters): string;
}