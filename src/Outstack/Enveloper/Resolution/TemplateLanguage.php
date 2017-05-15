<?php

namespace Outstack\Enveloper\Resolution;

interface TemplateLanguage
{
    public function render(string $templateContents, array $parameters): string;
}