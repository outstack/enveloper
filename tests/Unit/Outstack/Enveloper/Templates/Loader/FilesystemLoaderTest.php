<?php

namespace Outstack\Enveloper\Tests\Unit\Templates\Loader;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Outstack\Enveloper\Mail\Participants\Participant;
use Outstack\Enveloper\Templates\AttachmentListTemplate;
use Outstack\Enveloper\Templates\AttachmentTemplate;
use Outstack\Enveloper\Templates\Loader\Exceptions\InvalidConfigurationException;
use Outstack\Enveloper\Templates\Loader\FilesystemLoader;
use Outstack\Enveloper\Templates\Loader\TemplateNotFound;
use Outstack\Enveloper\Templates\ParticipantListTemplate;
use Outstack\Enveloper\Templates\ParticipantTemplate;
use Outstack\Enveloper\Templates\Template;
use Symfony\Component\Yaml\Yaml;

class FilesystemLoaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var FilesystemLoader
     */
    private $sut;

    public function setUp()
    {
        $this->filesystem = new Filesystem(new MemoryAdapter());

        $this->sut = new FilesystemLoader($this->filesystem);
    }

    public function test_throws_exception_when_not_found()
    {
        $this->expectException(TemplateNotFound::class);
        $this->assertEmpty($this->sut->find("does-not-exist"));
    }

    public function test_errors_on_invalid_yaml()
    {
        $meta = '[]/invalid-yaml::';
        $this->filesystem->write("template/template.meta.yml", $meta);

        $this->expectException(InvalidConfigurationException::class);
        $this->sut->find('template');

    }

    public function test_finds_simplest_possible_template()
    {
        $meta = Yaml::dump([
            'subject' => 'Welcome, {{ user.handle }}',
            'recipients' => [
                'to' => [
                    '{{ user.email }}'
                ]
            ],
            'content' => [
                'html' => 'new-user-welcome.html.twig'
            ]
        ]);

        $html = '<!DOCTYPE html><html><body><p>Welcome, {{ user.handle }}.</p></body></html>';

        $this->filesystem->write("new-user-welcome/new-user-welcome.html.twig", $html);
        $this->filesystem->write("new-user-welcome/new-user-welcome.meta.yml", $meta);

        $this->assertEquals(
            new Template(
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                $html,
                new AttachmentListTemplate([])
            ),
            $this->sut->find('new-user-welcome')
        );
    }

    public function test_finds_template_with_attachment()
    {
        $meta = Yaml::dump([
            'subject' => 'Welcome, {{ user.handle }}',
            'recipients' => [
                'to' => [
                    '{{ user.email }}'
                ]
            ],
            'content' => [
                'html' => 'new-user-welcome.html.twig'
            ],
            'attachments' => [
                ['contents' => '{{ contents }}', 'filename' => '{{ filename }}']
            ]
        ]);

        $html = '<!DOCTYPE html><html><body><p>Welcome, {{ user.handle }}.</p></body></html>';

        $this->filesystem->write("new-user-welcome/new-user-welcome.html.twig", $html);
        $this->filesystem->write("new-user-welcome/new-user-welcome.meta.yml", $meta);

        $this->assertEquals(
            new Template(
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                $html,
                new AttachmentListTemplate([
                    new AttachmentTemplate('{{ contents }}', '{{ filename }}')
                ])
            ),
            $this->sut->find('new-user-welcome')
        );
    }


    public function test_finds_template_with_attachment_iterator()
    {
        $meta = Yaml::dump([
            'subject' => 'Welcome, {{ user.handle }}',
            'recipients' => [
                'to' => [
                    '{{ user.email }}'
                ]
            ],
            'content' => [
                'html' => 'new-user-welcome.html.twig'
            ],
            'attachments' => [
                ['contents' => '{{ item.contents }}', 'filename' => '{{ item.filename }}', 'iterateOver' => 'attachments']
            ]
        ]);

        $html = '<!DOCTYPE html><html><body><p>Welcome, {{ user.handle }}.</p></body></html>';

        $this->filesystem->write("new-user-welcome/new-user-welcome.html.twig", $html);
        $this->filesystem->write("new-user-welcome/new-user-welcome.meta.yml", $meta);

        $this->assertEquals(
            new Template(
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                $html,
                new AttachmentListTemplate([
                    new AttachmentTemplate('{{ item.contents }}', '{{ item.filename }}', 'attachments')
                ])
            ),
            $this->sut->find('new-user-welcome')
        );
    }

    public function test_recipients_can_be_array_of_templated_name_and_email()
    {
        $meta = Yaml::dump([
            'subject' => 'Welcome, {{ user.handle }}',
            'from' => 'noreply@example.com',
            'recipients' => [
                'to' => [
                    '{{ user.email }}'
                ],
                'cc' => [
                    ['name' => '{{ user.name }}', 'email' => '{{ user.email }}'],
                    ['name' => null, 'email' => '{{ user.email }}'],
                    ['name' => null, 'email' => '{{ item.email }}', 'iterateOver' => 'administrators'],
                ]
            ],
            'content' => [
                'html' => 'new-user-welcome.html.twig'
            ]
        ]);

        $html = '<!DOCTYPE html><html><body><p>Welcome, {{ user.handle }}.</p></body></html>';

        $this->filesystem->write("new-user-welcome/new-user-welcome.html.twig", $html);
        $this->filesystem->write("new-user-welcome/new-user-welcome.meta.yml", $meta);

        $this->assertEquals(
            new Template(
                'Welcome, {{ user.handle }}',
                new ParticipantTemplate(null, 'noreply@example.com'),
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate(
                    [
                        new ParticipantTemplate('{{ user.name }}', '{{ user.email }}'),
                        new ParticipantTemplate(null, '{{ user.email }}'),
                        new ParticipantTemplate(null, '{{ item.email }}', 'administrators'),
                    ]
                ),
                new ParticipantListTemplate([]),
                null,
                $html,
                new AttachmentListTemplate([])
            ),
            $this->sut->find('new-user-welcome')
        );
    }
}