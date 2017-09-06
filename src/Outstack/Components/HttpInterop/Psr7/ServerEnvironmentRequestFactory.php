<?php

namespace Outstack\Components\HttpInterop\Psr7;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;

/**
 * This class takes a RequestInterface object and tries to create a
 * ServerRequestInterface object, creating minimal arrays mimicking
 * the superglobals.
 *
 * As it would be hard (if not impossible) to fill in every missing $_SERVER
 * parameter, those can be specified in the constructor.
 *
 * One use-case for this is for testing purposes, to use PSR-7 based client
 * tools to test applications, e.g. through a Symfony Kernel class rather than
 * over-the-wire using Nginx or Apache.
 */
class ServerEnvironmentRequestFactory
{
    /**
     * @var ServerRequestFactory
     */
    private $serverRequestFactory;
    /**
     * @var array
     */
    private $server;

    public function __construct(array $server)
    {
        $this->server = $server;
    }

    public function createServerRequest(RequestInterface $request): ServerRequestInterface
    {
        $server     = [];
        $get        = [];
        $post       = [];
        $cookies    = [];
        $files      = [];

        foreach ($request->getHeaders() as $key => $value) {
            $server[strtoupper($key)] = $value;
        }

        $server['HTTP_HOST']        = $request->getUri()->getHost();
        $server['REQUEST_METHOD']   = $request->getMethod();
        $server['QUERY_STRING']     = $request->getUri()->getQuery();
        $server['REQUEST_URI']      = $request->getUri()->getPath();

        parse_str($request->getUri()->getQuery(), $queryParams);
        foreach ($queryParams as $key => $value) {
            $get[$key] = $value;
        }

        return ServerRequestFactory::fromGlobals(
            array_merge($this->server, $server),
            $get,
            $post,
            $cookies,
            $files
        )->withBody($request->getBody());
    }
}