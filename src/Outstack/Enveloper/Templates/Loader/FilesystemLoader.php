<?php

namespace Outstack\Enveloper\Templates\Loader;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use Outstack\Enveloper\Templates\Loader\ConfigurationParser\TemplateConfiguration;
use Outstack\Enveloper\Templates\Loader\Exceptions\InvalidConfigurationException;
use Outstack\Enveloper\Templates\ParticipantListTemplate;
use Outstack\Enveloper\Templates\ParticipantTemplate;
use Outstack\Enveloper\Templates\Template;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

class FilesystemLoader implements TemplateLoader
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function find(string $name): Template
    {
        $configPath = "$name/$name.meta.yml";

        try {
            $config = Yaml::parse(
                $this->filesystem->read($configPath)
            );
        } catch (FileNotFoundException $e) {
            throw new TemplateNotFound($name);
        } catch (ParseException $e) {
            throw new InvalidConfigurationException("Failed parsing YAML at $configPath: {$e->getMessage()}", 0, $e);
        }

        $config = $this->normaliseConfig($config);

        $textTemplate = null;
        if (!is_null($config['content']['text'])) {
            $textTemplate = $this->filesystem->read("$name/{$config['content']['text']}");
        }

        $htmlTemplate = $this->filesystem->read("$name/{$config['content']['html']}");
        return new Template(
            $config['subject'],
            array_key_exists('from', $config) ? $this->parseRecipientTemplate($config['from']) : null,
            $this->parseRecipientListTemplate($config['recipients']['to']),
            $this->parseRecipientListTemplate($config['recipients']['cc']),
            $this->parseRecipientListTemplate($config['recipients']['bcc']),
            $textTemplate,
            $htmlTemplate
        );
    }

    private function parseRecipientListTemplate(array $recipients): ParticipantListTemplate
    {
        $templates = [];
        foreach ($recipients as $recipient) {
            $templates[] = $this->parseRecipientTemplate($recipient);
        }

        return new ParticipantListTemplate($templates);
    }

    private function parseRecipientTemplate(array $recipient): ParticipantTemplate
    {
        return new ParticipantTemplate($recipient['name'], $recipient['email'], $recipient['iterateOver'] ?? null);
    }

    private function normaliseConfig(array $config): array
    {
        $processor = new Processor();
        $configNode = new TemplateConfiguration();

        return $processor->processConfiguration($configNode, ['template' => $config]);
    }
}