<?php

namespace Outstack\Enveloper\Tests\Functional;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class AttachmentHandlingFunctionalTest extends AbstractApiTestCase
{
    use SwiftMailerAssertionTrait;

    protected $mailerSpy;

    public function setUp()
    {
        parent::setUp();

        $this->mailerSpy = self::$kernel->getContainer()->get(SwiftMailerInterface::class);
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
                        ['contents' => base64_encode('This is a note'), 'filename' => 'note.txt']
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

    public function test_large_attachment_sent()
    {
        $largeAttachment = random_bytes(1048576 * 7);
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'message-with-attachments',
                'parameters' => [
                    'email' => 'bob@example.com',
                    'attachments' => [
                        ['contents' => base64_encode($largeAttachment), 'filename' => 'random.txt']
                    ]
                ]
            ]))
        );

        $response = $this->client->sendRequest($request);

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertCountSentMessages(1);
        $this->assertMessageSent(
            function(\Swift_Message $message) use ($largeAttachment) {
                $expectedContents = implode("\r\n", str_split(base64_encode($largeAttachment), 76));
                $expected =
                    'Content-Type: application/octet-stream; name=random.txt' . "\r\n" .
                    'Content-Transfer-Encoding: base64' . "\r\n" .
                    'Content-Disposition: attachment; filename=random.txt' . "\r\n" . "\r\n" .
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

    public function test_static_attachment_sent()
    {
        $expectedAttachment = 'static attachment content';
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'message-with-static-attachments',
                'parameters' => [
                    'email' => 'bob@example.com'
                ]
            ]))
        );

        $response = $this->client->sendRequest($request);

        $this->assertEquals(204, $response->getStatusCode());

        $this->assertCountSentMessages(1);
        $this->assertMessageSent(
            function(\Swift_Message $message) use ($expectedAttachment) {
                $expectedContents = implode("\r\n", str_split(base64_encode($expectedAttachment), 76));
                $expected =
                    'Content-Type: application/octet-stream; name=attachment.txt' . "\r\n" .
                    'Content-Transfer-Encoding: base64' . "\r\n" .
                    'Content-Disposition: attachment; filename=attachment.txt' . "\r\n" . "\r\n" .
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