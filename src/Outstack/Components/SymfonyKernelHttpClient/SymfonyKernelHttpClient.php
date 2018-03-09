<?php

namespace Outstack\Components\SymfonyKernelHttpClient;

use Http\Client\Exception\HttpException;
use Http\Client\HttpClient;
use Outstack\Components\HttpInterop\Psr7\ServerEnvironmentRequestFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;

class SymfonyKernelHttpClient implements HttpClient
{
    /**
     * @var HttpKernelInterface
     */
    private $kernel;
    /**
     * @var HttpFoundationFactoryInterface
     */
    private $httpFoundationFactory;
    /**
     * @var HttpMessageFactoryInterface
     */
    private $httpMessageFactory;
    /**
     * @var ServerEnvironmentRequestFactory
     */
    private $serverRequestFactory;

    public function __construct(
        HttpKernelInterface $kernel,
        HttpFoundationFactoryInterface $httpFoundationFactory,
        HttpMessageFactoryInterface $httpMessageFactory,
        ServerEnvironmentRequestFactory $serverRequestFactory
    ) {
        $this->kernel = $kernel;
        $this->httpFoundationFactory = $httpFoundationFactory;
        $this->httpMessageFactory = $httpMessageFactory;
        $this->serverRequestFactory = $serverRequestFactory;
    }

    /**
     * Sends a PSR-7 request.
     *
     * @param RequestInterface $psrRequest
     *
     * @return ResponseInterface
     *
     * @throws \Http\Client\Exception If an error happens during processing the request.
     * @throws \Exception             If processing the request is impossible (eg. bad configuration).
     */
    public function sendRequest(RequestInterface $psrRequest)
    {
        $serverRequest = $this->serverRequestFactory->createServerRequest($psrRequest);

        $foundationRequest = $this->httpFoundationFactory->createRequest(
            $serverRequest
        );
        $foundationResponse = $this->kernel->handle($foundationRequest);

        if ($this->kernel instanceof Kernel) {
            $this->kernel->terminate($foundationRequest, $foundationResponse);
            $this->kernel->shutdown();
        }

        $psrResponse = $this->httpMessageFactory->createResponse($foundationResponse);

        if (!$foundationResponse->isSuccessful()) {
            throw new HttpException(
                "Kernel returned non-successful status code {$foundationResponse->getStatusCode()}",
                $psrRequest,
                $psrResponse
            );
        }
        return $psrResponse;
    }

}
