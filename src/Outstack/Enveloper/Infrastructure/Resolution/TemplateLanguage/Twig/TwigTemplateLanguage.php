<?php

namespace Outstack\Enveloper\Infrastructure\Resolution\TemplateLanguage\Twig;

use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLanguage;
use Twig_Environment;

class TwigTemplateLanguage implements TemplateLanguage
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

    public function render(string $templateContents, object $parameters): string
    {
        return $this->twig->createTemplate($templateContents)->render((array) $parameters);
    }
}