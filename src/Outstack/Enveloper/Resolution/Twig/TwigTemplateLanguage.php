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

        $twig->addExtension(new TwigEnveloperExtension());
        $this->twig = $twig;
    }

    public function render(string $templateContents, object $parameters): string
    {
        return $this->twig->createTemplate($templateContents)->render((array) $parameters);
    }
}