<?php

namespace Outstack\Enveloper\Tests\Unit\Templates\Loader;

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Outstack\Enveloper\Domain\Resolution\Templates\AttachmentListTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\AttachmentTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\ParticipantListTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\ParticipantTemplate;
use Outstack\Enveloper\Domain\Resolution\Templates\Template;
use Outstack\Enveloper\Domain\Resolution\Templates\TemplateNotFound;
use Outstack\Enveloper\Infrastructure\Resolution\TemplateLoader\Filesystem\Exceptions\InvalidConfigurationException;
use Outstack\Enveloper\Infrastructure\Resolution\TemplateLoader\Filesystem\FilesystemLoader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

class FilesystemLoaderTest extends TestCase
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
                null,
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                null,
                'new-user-welcome.html.twig',
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
                null,
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                null,
                'new-user-welcome.html.twig',
                $html,
                new AttachmentListTemplate([
                    new AttachmentTemplate(false, '{{ contents }}', '{{ filename }}')
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
                null,
                'Welcome, {{ user.handle }}',
                null,
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                null,
                'new-user-welcome.html.twig',
                $html,
                new AttachmentListTemplate([
                    new AttachmentTemplate(false, '{{ item.contents }}', '{{ item.filename }}', 'attachments')
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
                null,
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
                null,
                'new-user-welcome.html.twig',
                $html,
                new AttachmentListTemplate([])
            ),
            $this->sut->find('new-user-welcome')
        );
    }

    public function test_schema_is_loaded()
    {
        $meta = Yaml::dump([
            'subject' => 'Welcome, {{ user.handle }}',
            'from' => 'noreply@example.com',
            'recipients' => [
                'to' => [
                    '{{ user.email }}'
                ]
            ],
            'content' => [
                'html' => 'new-user-welcome.html.twig'
            ]
        ]);

        $schema = <<<SCHEMA
{
  "properties": {
    "user": {
      "type": "object",
      "properties": {
        "handle": {
          "type": "string"
        }
      }
    }
  }
}
SCHEMA;


        $html = '<!DOCTYPE html><html><body><p>Welcome, {{ user.handle }}.</p></body></html>';

        $this->filesystem->write("new-user-welcome/new-user-welcome.html.twig", $html);
        $this->filesystem->write("new-user-welcome/new-user-welcome.meta.yml", $meta);
        $this->filesystem->write("new-user-welcome/new-user-welcome.schema.json", $schema);


        $this->assertEquals(
            new Template(
                (object) [
                    'properties' => (object) [
                        'user' => (object) [
                            'type' => 'object',
                            'properties' => (object) [
                                'handle' => (object) [
                                    'type' => 'string'
                                ]
                            ]
                        ]
                    ]
                ],
                'Welcome, {{ user.handle }}',
                new ParticipantTemplate(null, 'noreply@example.com'),
                new ParticipantListTemplate([new ParticipantTemplate(null, '{{ user.email }}')]),
                new ParticipantListTemplate([]),
                new ParticipantListTemplate([]),
                null,
                null,
                'new-user-welcome.html.twig',
                $html,
                new AttachmentListTemplate([])
            ),
            $this->sut->find('new-user-welcome')
        );
    }
}