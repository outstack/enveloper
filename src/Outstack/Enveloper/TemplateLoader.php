<?php

namespace Outstack\Enveloper;

interface TemplateLoader
{
    public function loadTemplate(string $name);
}