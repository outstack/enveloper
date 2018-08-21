<?php

namespace Outstack\Enveloper\Tests\Functional;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class LogHistoryFunctionalTest extends AbstractApiTestCase
{
    public function test_log_of_useful_information_is_recorded()
    {
        try {
            $this->client->sendRequest(
                new Request(
                    "{$this->baseUri}/outbox",
                    'POST',
                    $this->convertToStream(json_encode([
                        'template' => 'message-with-schema',
                        'parameters' => (object) []
                    ]))
                )
            );
        } catch (HttpException $e) {
        } // Expect a 400, assert it's the right error type via log data below

        $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/outbox",
                'POST',
                $this->convertToStream(json_encode([
                    'template' => 'message-with-schema',
                    'parameters' => [
                        'name' => 'Bob',
                        'email' => 'bob@example.com'
                    ]
                ]))
            )
        );

        $response = $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/logs",
                'GET',
                $this->convertToStream('')
            )
        );

        $this->assertSame(200, $response->getStatusCode());

        $actual = json_decode((string)$response->getBody());

        $this->assertJsonDocumentMatchesSchema($actual, 'endpoints/logs/get.responseBody.schema.json');
        $this->assertCount(2, $actual->items);


        $response = $this->client->sendRequest(
            new Request(
                $actual->items[0]->{'@id'},
                'GET',
                $this->convertToStream('')
            )
        );
        $this->assertJsonDocumentMatchesSchema(json_decode((string)$response->getBody()), 'resources/logs/failed-json-schema-validation.schema.json');

        $response = $this->client->sendRequest(
            new Request(
                $actual->items[1]->{'@id'},
                'GET',
                $this->convertToStream('')
            )
        );
        $this->assertJsonDocumentMatchesSchema(json_decode((string)$response->getBody()), 'resources/logs/message-sent.schema.json');


    }
}