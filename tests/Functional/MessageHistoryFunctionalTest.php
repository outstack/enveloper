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

        $this->assertJsonDocumentMatchesSchema($actual, 'outbox_sent_messages_list.json');
        $this->assertCount(1, $actual->items);
        $this->assertSame('Hello, Bob',             $actual->items[0]->resolved->subject);
        $this->assertSame('simplest-test-message',  $actual->items[0]->template);
        $this->assertEquals(
            (object) [
                'name' => 'Bob',
                'email' => 'bob@example.com'
            ],
            $actual->items[0]->parameters
        );

        $this->assertEquals(
            '<html>
    <body>
        <p>Hello, Bob</p>
    </body>
</html>',
            $this->client->sendRequest(
                new Request(
                    $actual->items[0]->resolved->content->{'@id'},
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