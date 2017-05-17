<?php

namespace Outstack\Enveloper\Tests\Unit;

use Outstack\Enveloper\Mail\Participants\EmailAddress;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Mail\Participants\ParticipantList;
use Outstack\Enveloper\Resolution\MessageResolver;
use Outstack\Enveloper\Resolution\ParticipantListResolver;
use Outstack\Enveloper\Resolution\ParticipantResolver;
use Outstack\Enveloper\ResolvedMessage;
use Outstack\Enveloper\Templates\ParticipantListTemplate;
use Outstack\Enveloper\Templates\ParticipantTemplate;
use Outstack\Enveloper\Templates\Template;
use Outstack\Enveloper\Tests\Unit\Resolution\AbstractResolutionUnitTest;

class MessageResolverTest extends AbstractResolutionUnitTest
{
    /**
     * @var MessageResolver
     */
    private $sut;

    public function setUp()
    {
        parent::setUp();
    }

    public function test_it_resolves_simplest_message()
    {
        $recipientResolver = new ParticipantResolver($this->language);
        $this->sut = new MessageResolver(
            $this->language,
            new ParticipantListResolver($recipientResolver),
            $recipientResolver,
            'noreply@example.com',
            null
        );

        $message = $this->sut->resolve(
            new Template(
                'Welcome, {{ user.name }}',
                new ParticipantTemplate(null, 'noreply@example.com'),
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                'Welcome to app, {{ user.name }}',
                '<p>Welcome to app {{ user.name }}'
            ),
            [
                'user' => [
                    'name'  => 'john',
                    'email' => 'john@example.com'
                ]
            ]
        );

        $this->assertEquals('Welcome, john', $message->getSubject());
    }

    public function test_it_uses_default_sender_email_if_blank()
    {
        $recipientResolver = new ParticipantResolver($this->language);
        $this->sut = new MessageResolver(
            $this->language,
            new ParticipantListResolver($recipientResolver),
            $recipientResolver,
            'noreply@example.com',
            'Do not reply'
        );

        $message = $this->sut->resolve(
            new Template(
                'Welcome, {{ user.name }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                'Welcome to app, {{ user.name }}',
                '<p>Welcome to app {{ user.name }}'
            ),
            [
                'user' => [
                    'name'  => 'john',
                    'email' => 'john@example.com'
                ]
            ]
        );

        $this->assertEquals(new Participant('Do not reply', new EmailAddress('noreply@example.com')), $message->getSender());
    }

    public function test_it_resolves_message_with_multiple_templated_recipients()
    {
        $recipientResolver = new ParticipantResolver($this->language);
        $this->sut = new MessageResolver(
            $this->language,
            new ParticipantListResolver($recipientResolver),
            $recipientResolver,
            'noreply@example.com',
            null
        );

        $message = $this->sut->resolve(
            new Template(
                'Welcome, {{ user.name }}',
                new ParticipantTemplate(null, 'noreply@example.com'),
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([new ParticipantTemplate('{{ name }}', '{{ email }}', 'administrators')]),
                new ParticipantListTemplate([]),
                'Welcome to app, {{ user.name }}',
                '<p>Welcome to app {{ user.name }}'
            ),
            [
                'user' => [
                    'name'  => 'john',
                    'email' => 'john@example.com'
                ],
                'administrators' => [
                    ['name' => 'Admin 1', 'email' => 'admin1@example.com'],
                    ['name' => 'Admin 2', 'email' => 'admin2@example.com'],
                ]
            ]
        );

        $this->assertEquals(
            new ParticipantList(
                [
                    new Participant('Admin 1', new EmailAddress('admin1@example.com')),
                    new Participant('Admin 2', new EmailAddress('admin2@example.com')),
                ]
            ),
            $message->getCc()
        );

        $this->assertEquals(new Participant(null, new EmailAddress('noreply@example.com')), $message->getSender());

    }
}