<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class EmailSendingFunctionalTest extends AbstractApiTestCase
{
    use SwiftMailerAssertionTrait;

    public function setUp()
    {
        parent::setUp();

        $profiler = self::$kernel->getContainer()->get('profiler');
        $profiler->enable();
        $this->setMessageDataCollector($profiler->get('swiftmailer'));
    }

    public function test_page_not_found_is_nicely_formatted()
    {
        $request = new Request(
            '/not-found',
            'GET'
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(404, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Not Found',
                'detail' => 'No matching action was found to handle the request',
                'status' => 404
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");

    }

    public function test_debugging_email_sent()
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

        $response = $this->client->sendRequest($request);
        $this->assertEquals(204, $response->getStatusCode());

        $this->assertCountSentMessages(1);
        $this->assertMessageSent(
            function(\Swift_Message $message) {
                return
                    1 === count($message->getTo()) &&
                    $this->doesToIncludeEmailAddress($message, 'bob@example.com') &&
                    $this->messageWasFromContact($message, 'test@example.com', 'Test Default Sender');
            }
        );
    }
}