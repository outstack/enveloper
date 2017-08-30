<?php

namespace Outstack\Enveloper\Tests\Functional;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class MessageHistoryFunctionalTest extends AbstractApiTestCase
{
    public function test_message_history_is_available_when_sent()
    {
        $convertToStream = function($str) {
            $stream = fopen("php://temp", 'r+');
            fputs($stream, $str);
            rewind($stream);
            return $stream;
        };
        $request = new Request(
            '/outbox',
            'POST',
            $convertToStream(json_encode([
                'template' => 'simplest-test-message',
                'parameters' => [
                    'name' => 'Bob',
                    'email' => 'bob@example.com'
                ]
            ]))
        );

        $this->client->sendRequest($request);

    }
}