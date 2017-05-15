<?php

namespace Outstack\Enveloper\Templates\Loader;

use Outstack\Enveloper\Templates\Template;

interface TemplateLoader
{
    public function find(string $name): Template;
}