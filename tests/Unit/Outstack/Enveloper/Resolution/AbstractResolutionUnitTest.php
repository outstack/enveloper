<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Resolution\TemplateLanguage;
use Outstack\Enveloper\Resolution\Twig\TwigTemplateLanguage;

abstract class AbstractResolutionUnitTest extends \PHPUnit_Framework_TestCase
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