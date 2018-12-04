<?php

namespace Outstack\Enveloper\Application;


use Outstack\Enveloper\Domain\Resolution\MessageResolver;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLoader;

class PreviewEmail
{
    /**
     * @var MessageResolver
     */
    private $messageResolver;
    /**
     * @var TemplateLoader
     */
    private $templateLoader;

    public function __construct(MessageResolver $messageResolver, TemplateLoader $templateLoader)
    {
        $this->messageResolver = $messageResolver;
        $this->templateLoader = $templateLoader;
    }

    public function __invoke(string $template, object $parameters)
    {
        return $this->messageResolver->resolve($this->templateLoader->find($template), $parameters);
    }
}