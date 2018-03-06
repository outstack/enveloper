<?php

namespace Outstack\Enveloper\PipeprintBridge;

use League\Flysystem\Filesystem;

class TemplatePipelineFactory
{

    /**
     * @var \Twig_Environment
     */
    private $twig;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem, \Twig_Environment $twig)
    {
        $this->twig = $twig;
        $this->filesystem = $filesystem;
    }

    public function create(?string $pipeprintUrl)
    {
        if ($pipeprintUrl) {
            return new PipeprintPipeline($this->filesystem, $pipeprintUrl);
        }

        return new TwigTemplatePipeline($this->twig);
    }
}