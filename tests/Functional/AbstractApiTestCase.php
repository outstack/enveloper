<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\HttpClient;
use Outstack\Components\HttpInterop\Psr7\ServerEnvironmentRequestFactory;
use Outstack\Components\SymfonyKernelHttpClient\SymfonyKernelHttpClient;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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
    }
}