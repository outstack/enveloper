<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\HttpClient;
use Outstack\Components\HttpInterop\Psr7\ServerEnvironmentRequestFactory;
use Outstack\Components\SymfonyKernelHttpClient\SymfonyKernelHttpClient;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class AbstractApiTestCase extends KernelTestCase
{
    /**
     * @var HttpClient
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();

        self::$kernel = static::createKernel();
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

        $this->executeConsoleCommand("doctrine:database:create");
        $this->executeConsoleCommand("doctrine:schema:create");


    }

    protected function executeConsoleCommand($cmd)
    {
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);
        $application->setCatchExceptions(false);
        $application->run(new StringInput("$cmd --env=test --no-interaction"), new NullOutput());
    }

}