<?php

namespace Outstack\Enveloper\Infrastructure\Resolution\TemplateLanguage\Twig;

class TwigEnveloperExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return [
            'base64_decode' => new \Twig_SimpleFilter('base64_decode', 'base64_decode')
        ];
    }

}