<?php

namespace Outstack\Enveloper\PipeprintBridge;

use Outstack\Enveloper\Templates\TemplatePipeline;
use Twig_Environment;

class TwigTemplatePipeline implements TemplatePipeline
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    public function __construct(Twig_Environment $twig = null)
    {
        if (is_null($twig)) {
            $twig = new Twig_Environment(new \Twig_Loader_Chain());
        }

        $this->twig = $twig;
    }

    public function render(string $templateName, string $templateContents, object $parameters): string
    {
        return $this->twig->createTemplate($templateContents)->render((array) $parameters);
    }

}