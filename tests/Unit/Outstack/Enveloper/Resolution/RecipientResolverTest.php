<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Domain\Email\Participants\EmailAddress;
use Outstack\Enveloper\Domain\Email\Participants\Participant;
use Outstack\Enveloper\Domain\Resolution\ParticipantResolver;
use Outstack\Enveloper\Domain\Resolution\Templates\ParticipantTemplate;

class RecipientResolverTest extends AbstractResolutionUnitTest
{
    /**
     * @var ParticipantResolver
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->sut = new ParticipantResolver($this->language);
    }

    public function test_recipient_without_name_resolved()
    {
        $this->assertEquals(
            new Participant(null, new EmailAddress('admin1@example.com')),

            $this->sut->resolveRecipient(
                new ParticipantTemplate(null, 'admin{{ number }}@{{ domain}}'),
                (object) [
                    'number' => 1,
                    'domain' => 'example.com'
                ]
            )
        );
    }
    public function test_recipient_with_name_resolved()
    {
        $this->assertEquals(
            new Participant('Admin Number 1', new EmailAddress('admin1@example.com')),

            $this->sut->resolveRecipient(
                new ParticipantTemplate('Admin Number {{ number }}', 'admin1@example.com'),
                (object) [
                    'number' => 1
                ]
            )
        );
    }
}