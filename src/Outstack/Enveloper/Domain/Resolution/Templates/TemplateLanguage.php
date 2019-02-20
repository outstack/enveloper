<?php

namespace Outstack\Enveloper\Domain\Resolution\Templates;

interface TemplateLanguage
{
    public function render(string $templateContents, object $parameters): string;
}