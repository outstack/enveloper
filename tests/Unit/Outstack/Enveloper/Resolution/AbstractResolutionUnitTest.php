<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Resolution\TemplateLanguage;
use Outstack\Enveloper\Resolution\Twig\TwigTemplateLanguage;
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