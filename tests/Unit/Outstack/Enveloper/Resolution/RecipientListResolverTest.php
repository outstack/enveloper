<?php

namespace Outstack\Enveloper\Tests\Unit\Resolution;

use Outstack\Enveloper\Mail\Participants\EmailAddress;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Mail\Participants\ParticipantList;
use Outstack\Enveloper\Resolution\ParticipantListResolver;
use Outstack\Enveloper\Resolution\ParticipantResolver;
use Outstack\Enveloper\Templates\ParticipantListTemplate;
use Outstack\Enveloper\Templates\ParticipantTemplate;

class RecipientListResolverTest extends AbstractResolutionUnitTest
{
    /**
     * @var ParticipantListResolver
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
        $this->sut = new ParticipantListResolver(new ParticipantResolver($this->language));
    }

    public function test_resolves_single_recipient()
    {
        $this->assertEquals(
            new ParticipantList(
                [
                    new Participant(null, new EmailAddress('admin1@example.com')),
                ]
            ),

            $this->sut->resolveParticipantList(

                new ParticipantListTemplate(
                    [
                        new ParticipantTemplate(null, 'admin{{ number }}@{{ domain}}')
                    ]
                ),
                (object) [
                    'number' => 1,
                    'domain' => 'example.com'
                ]
            )
        );
    }
    public function test_resolves_mixed_single_and_iterated_recipient()
    {
        $this->assertEquals(
            new ParticipantList(
                [
                    new Participant(null, new EmailAddress('admin1@example.com')),
                    new Participant('Admin 2', new EmailAddress('admin2@example.com')),
                    new Participant('Admin 3', new EmailAddress('admin3@example.com')),
                ]
            ),

            $this->sut->resolveParticipantList(

                new ParticipantListTemplate(
                    [
                        new ParticipantTemplate(null, 'admin{{ number }}@{{ domain}}'),
                        new ParticipantTemplate('{{ name }}', '{{ email }}', 'administrators')
                    ]
                ),
                (object) [
                    'number' => 1,
                    'domain' => 'example.com',
                    'administrators' => [
                        ['name' => 'Admin 2', 'email' => 'admin2@example.com'],
                        ['name' => 'Admin 3', 'email' => 'admin3@example.com'],
                    ]
                ]
            )
        );
    }
}