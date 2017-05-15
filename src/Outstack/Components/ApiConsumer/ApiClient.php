<?php

namespace Outstack\Components\ApiConsumer;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;
use Http\Message\StreamFactory;
use Http\Message\UriFactory;

class ApiClient
{
    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var StreamFactory
     */
    private $streamFactory;

    /**
     * @var UriFactory
     */
    private $uriFactory;
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(
        RequestFactory $requestFactory,
        StreamFactory $streamFactory,
        UriFactory $uriFactory,
        HttpClient $httpClient
    ) {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
        $this->httpClient = $httpClient;
    }

    public function request(string $method, string $uri, ?string $body, array $headers = [])
    {
        $uri = $this->uriFactory->createUri($uri);
        $stream = $this->streamFactory->createStream($body);
        $request = $this->requestFactory->createRequest($method, $uri, $headers, $stream);

        return $this->httpClient->sendRequest($request);
    }
}