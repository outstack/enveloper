<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Mail\Attachments\Attachment;
use Outstack\Enveloper\Mail\Attachments\AttachmentList;
use Outstack\Enveloper\Resolution\AttachmentListResolver;
use Outstack\Enveloper\Resolution\AttachmentResolver;
use Outstack\Enveloper\Templates\AttachmentListTemplate;
use Outstack\Enveloper\Templates\AttachmentTemplate;

class AttachmentListResolverTest extends AbstractResolutionUnitTest
{
    /**
     * @var AttachmentListResolver
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->sut = new AttachmentListResolver(
            new AttachmentResolver($this->language)
        );
    }

    public function test_resolves_template_with_iterated_value()
    {
        $this->assertEquals(
            new AttachmentList(
                [
                    new Attachment('attachment 1', 'a1.txt'),
                    new Attachment('attachment 2', 'a2.txt'),
                ]
            ),
            $this->sut->resolveAttachmentList(
                new AttachmentListTemplate(
                    [
                        new AttachmentTemplate('{{ item.data }}', '{{ item.filename }}', 'attachments')
                    ]
                ),
                (object) [
                    'attachments' => [
                        ['data' => 'attachment 1', 'filename' => 'a1.txt'],
                        ['data' => 'attachment 2', 'filename' => 'a2.txt']
                    ]
                ]
            )
        );
    }

    public function test_resolves_multiple_attachments()
    {
        $this->assertEquals(
            new AttachmentList(
                [
                    new Attachment('attachment 1', 'a1.txt'),
                    new Attachment('attachment 2', 'a2.txt'),
                ]
            ),
            $this->sut->resolveAttachmentList(
                new AttachmentListTemplate(
                    [
                        new AttachmentTemplate('{{ attachments[0].data }}', '{{ attachments[0].filename }}'),
                        new AttachmentTemplate('{{ attachments[1].data }}', '{{ attachments[1].filename }}')
                    ]
                ),
                (object) [
                    'attachments' => [
                        ['data' => 'attachment 1', 'filename' => 'a1.txt'],
                        ['data' => 'attachment 2', 'filename' => 'a2.txt']
                    ]
                ]
            )
        );
    }

}