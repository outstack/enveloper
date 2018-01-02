<?php

namespace Outstack\Enveloper\Templates;

interface TemplatePipeline
{
    public function render(string $templateName, string $templateContents, object $parameters): string;
}