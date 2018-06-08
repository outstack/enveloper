<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Mail\Attachments\Attachment;
use Outstack\Enveloper\Resolution\AttachmentResolver;
use Outstack\Enveloper\Templates\AttachmentTemplate;

class AttachmentResolverTest extends AbstractResolutionUnitTest
{
    /**
     * @var AttachmentResolver
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->sut = new AttachmentResolver($this->language);
    }

    public function test_simple_txt_resolved()
    {
        $this->assertEquals(
            new Attachment(
                'part 1 - part 2',
                '2part.txt'
            ),
            $this->sut->resolve(
                new AttachmentTemplate(
                    false,
                    '{{ string1 }} - {{ string2 }}',
                    '{{ string3 }}.txt',
                    null
                ),
                (object) [
                    'string1' => 'part 1',
                    'string2' => 'part 2',
                    'string3' => '2part'
                ]
            )
        );
    }
}