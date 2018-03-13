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
                '/outbox',
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
            '/outbox',
            'GET',
                $this->convertToStream('')
            )
        );

        $this->assertSame(200, $response->getStatusCode());

        $actual = json_decode((string)$response->getBody(), true);

        $this->assertJsonDocumentMatchesSchema($actual['items'][0], $this->getSchema('outbox_sent_message.json'));
        $this->assertStringMatchesFormat(
            '%x%x%x%x%x%x%x%x-%x%x%x%x-%x%x%x%x-%x%x%x%x-%x%x%x%x%x%x%x%x%x%x%x%x',
            $actual[0]['id']
        );
        unset($actual[0]['id']);

        $this->assertEquals([
            [
                'template' => 'simplest-test-message',
                'parameters' => [
                    'name' => 'Bob',
                    'email' => 'bob@example.com'
                ],
                'resolved' => [
                    'subject' => 'Hello, Bob',
                    'sender' => [
                        'name' => 'Test Default Sender',
                        'email' => 'test@example.com'
                    ],
                    'recipients' => [
                        'to' => [
                            [
                                'name' => null,
                                'email' => 'bob@example.com'
                            ]
                        ],
                        'cc' => [],
                        'bcc' => []
                    ],
                    'content' => [
                        'text' => 'Hello, Bob',
                        'html' => '<html>
    <body>
        <p>Hello, Bob</p>
    </body>
</html>'
                    ]
                ]
            ]
        ], $actual);

$this->client->sendRequest(
            new Request(
                '/outbox',
                'DELETE',
                $this->convertToStream('')
            )
        );

        $response = $this->client->sendRequest(
            new Request(
            '/outbox',
            'GET',
                $this->convertToStream('')
            )
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertEquals([], json_decode((string) $response->getBody(), true));

    }
}