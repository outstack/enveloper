<?php

namespace Outstack\Enveloper\Resolution\Twig;

use Outstack\Enveloper\Resolution\TemplateLanguage;
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

    public function render(string $templateContents, array $parameters): string
    {
        return $this->twig->createTemplate($templateContents)->render($parameters);
    }
}