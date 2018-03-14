<?php

namespace Outstack\Enveloper\Tests\Functional;

use Helmich\JsonAssert\JsonAssertions;
use Http\Client\HttpClient;
use League\JsonGuard\ValidationError;
use Outstack\Components\HttpInterop\Psr7\ServerEnvironmentRequestFactory;
use Outstack\Components\SymfonyKernelHttpClient\SymfonyKernelHttpClient;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractApiTestCase extends KernelTestCase
{
    /**
     * @var HttpClient
     */
    protected $client;

    protected $baseUri = "http://enveloper.test";

    protected function assertJsonDocumentMatchesSchema($document, $schema)
    {
        $projectDir = __DIR__.'/../../';
        $dereferencer = \League\JsonReference\Dereferencer::draft6();
        $schema = $dereferencer->dereference("file://{$projectDir}/schemata/$schema");

        $validator = new \League\JsonGuard\Validator($document, $schema);

        $this->assertFalse(
            $validator->fails(),
            implode(
                "\n",
                array_map(
                    function(ValidationError $error) {
                        return $error->getDataPath() . ": " . $error->getMessage() . json_encode($error->getData());
                    },
                    $validator->errors()
                )
            )
        );
    }


    public function setUp()
    {
        parent::setUp();

        self::$kernel = static::createKernel(['environment' => 'test']);
        self::$kernel->boot();

        $this->client = new SymfonyKernelHttpClient(
            self::$kernel,
            new HttpFoundationFactory(),
            new DiactorosFactory(),
            new ServerEnvironmentRequestFactory([])
        );

        $dbFile = self::$kernel->getRootDir() . '/../tests/data/enveloper_test.sqlite';
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $this->executeConsoleCommand("doctrine:database:create -v");
        $this->executeConsoleCommand("doctrine:schema:create -v");


    }

    protected function executeConsoleCommand($cmd)
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->run(new StringInput("$cmd --env=test --no-interaction"), new NullOutput());
    }

    protected function convertToStream(string $str)
    {
        $stream = fopen("php://temp", 'r+');
        fputs($stream, $str);
        rewind($stream);
        return $stream;
    }

}