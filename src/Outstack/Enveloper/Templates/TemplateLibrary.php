<?php

namespace Outstack\Enveloper\Templates;

class TemplateLibrary
{
    private $templates;

    public function configure(string $templateName, Template $template)
    {
        $this->templates[$templateName] = $template;
    }

    public function find(string $templateName)
    {
        return $this->templates[$templateName];
    }
}