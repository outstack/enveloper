<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Domain\Resolution\Templates\TemplateLanguage;
use Outstack\Enveloper\Infrastructure\Resolution\TemplateLanguage\Twig\TwigTemplateLanguage;
use PHPUnit\Framework\TestCase;

abstract class AbstractResolutionUnitTest extends TestCase
{
    /**
     * @var TemplateLanguage
     */
    protected $language;

    public function setUp()
    {
        $this->language = new TwigTemplateLanguage();
    }
}