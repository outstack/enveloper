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
    public function test_message_history_is_available_when_sent_and_can_be_reset()
    {
        $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/outbox",
                'POST',
                $this->convertToStream(json_encode([
                    'template' => 'simplest-test-message',
                    'parameters' => [
                        'name' => 'Bob',
                        'email' => 'bob@example.com'
                    ]
                ]))
            )
        );

        $response = $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/outbox",
            'GET',
                $this->convertToStream('')
            )
        );

        $this->assertSame(200, $response->getStatusCode());

        $actual = json_decode((string)$response->getBody());

        $this->assertJsonDocumentMatchesSchema($actual, 'endpoints/outbox/get.responseBody.schema.json');
        $this->assertCount(1, $actual->items);
        $latestEmailRequest = $actual->items[0];
        $this->assertSame('simplest-test-message',  $latestEmailRequest->template);
        $this->assertEquals(
            (object) [
                'name' => 'Bob',
                'email' => 'bob@example.com'
            ],
            $latestEmailRequest->parameters
        );

        $deliveryAttempts = json_decode((string) $this->client->sendRequest(
            new Request(
                "{$latestEmailRequest->deliveryAttempts}",
                'GET',
                $this->convertToStream('')
            )
        )->getBody());

        $this->assertJsonDocumentMatchesSchema($deliveryAttempts, 'endpoints/outbox/deliveryAttempts/get.responseBody.schema.json');
        $this->assertCount(1, $deliveryAttempts->items);

        $this->client->sendRequest(
            new Request(
                $deliveryAttempts->items[0]->{'@id'},
                'GET',
                $this->convertToStream('')
            )
        );

        $resolved = $deliveryAttempts->items[0]->resolved;

        $this->assertSame('Hello, Bob', $resolved->subject);

        $this->assertEquals(
            '<html>
    <body>
        <p>Hello, Bob</p>
    </body>
</html>',
            $this->client->sendRequest(
                new Request(
                    $resolved->content->{'@id'},
                    'GET',
                    $this->convertToStream(''),
                    ['HTTP_ACCEPT' => 'text/html']
                )
            )->getBody()->__toString()
        );

        $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/outbox",
                'DELETE',
                $this->convertToStream('')
            )
        );

        $response = $this->client->sendRequest(
            new Request(
                "{$this->baseUri}/outbox",
            'GET',
                $this->convertToStream('')
            )
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals(
            (object) ['items' => []],
            json_decode((string) $response->getBody())
        );

    }
}