<?php

namespace Outstack\Enveloper\Infrastructure\Resolution\TemplatePipeline;

use League\Flysystem\Filesystem;
use Outstack\Enveloper\Infrastructure\Resolution\TemplatePipeline\Pipeprint\PipeprintPipeline;
use Outstack\Enveloper\Infrastructure\Resolution\TemplatePipeline\Twig\TwigTemplatePipeline;

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