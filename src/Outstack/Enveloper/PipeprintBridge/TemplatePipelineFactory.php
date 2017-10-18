<?php

namespace Outstack\Enveloper\PipeprintBridge;

class TemplatePipelineFactory
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function create(?string $pipeprintUrl)
    {
        if ($pipeprintUrl) {
            return new PipeprintPipeline($pipeprintUrl);
        }

        return new TwigTemplatePipeline($this->twig);
    }
}