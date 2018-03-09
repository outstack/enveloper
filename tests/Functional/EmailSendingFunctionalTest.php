<?php

namespace Outstack\Enveloper\Tests\Functional;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class EmailSendingFunctionalTest extends AbstractApiTestCase
{
    use SwiftMailerAssertionTrait;

    protected $mailerSpy;

    public function setUp()
    {
        parent::setUp();

        $this->mailerSpy = self::$kernel->getContainer()->get(SwiftMailerInterface::class);
    }

    public function test_email_without_text_version()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'without-text-version',
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
    public function test_debugging_email_sent()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
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

    public function test_email_with_include()
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
                'template' => 'template-with-include',
                'parameters' => (object) []
            ]))
        );

        $response = $this->client->sendRequest($request);
        $this->assertEquals(204, $response->getStatusCode());

        $this->assertCountSentMessages(1);
        $this->assertMessageSent(
            function(\Swift_Message $message) {
                return
                    1 === count($message->getTo()) &&
                    false !== strpos($message->getBody(), 'Included file') &&
                    $this->doesToIncludeEmailAddress($message, 'test@example.com') &&
                    $this->messageWasFromContact($message, 'test@example.com', 'Test Default Sender');
            }
        );
    }

    public function test_mjml_email_sent()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'mjml-example',
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
                    false !== strpos($message->getBody(), '<!doctype html>') &&
                    1 === count($message->getTo()) &&
                    $this->doesToIncludeEmailAddress($message, 'bob@example.com') &&
                    $this->messageWasFromContact($message, 'test@example.com', 'Test Default Sender');
            }
        );
    }

    public function test_attachments_sent()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'message-with-attachments',
                'parameters' => [
                    'email' => 'bob@example.com',
                    'attachments' => [
                        ['contents' => 'This is a note', 'filename' => 'note.txt']
                    ]
                ]
            ]))
        );

        $response = $this->client->sendRequest($request);
        $this->assertEquals(204, $response->getStatusCode());

        $this->assertCountSentMessages(1);
        $this->assertMessageSent(
            function(\Swift_Message $message) {
                $expectedContents = base64_encode('This is a note');
                $expected =
                    'Content-Type: application/octet-stream; name=note.txt' . "\r\n" .
                    'Content-Transfer-Encoding: base64' . "\r\n" .
                    'Content-Disposition: attachment; filename=note.txt' . "\r\n" . "\r\n" .
                    $expectedContents . "\r\n" . "\r\n" .
                    '--'
                    ;

                foreach (explode($message->getBoundary(), (string) $message) as $part) {
                    if (trim($part) == trim($expected)) {
                        return true;
                    }
                }

                throw new \LogicException("No matching attachment found");
            }
        );
    }
}