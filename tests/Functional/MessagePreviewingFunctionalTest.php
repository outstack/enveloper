<?php

namespace Outstack\Enveloper\Tests\Functional;

use Outstack\Enveloper\SwiftMailerBridge\SwiftMailerInterface;
use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class MessagePreviewingFunctionalTest extends AbstractApiTestCase
{
    use SwiftMailerAssertionTrait;

    protected $mailerSpy;

    public function setUp()
    {
        parent::setUp();

        $this->mailerSpy = self::$kernel->getContainer()->get(SwiftMailerInterface::class);
    }

    public function test_previewing_html_version()
    {
        $request = new Request(
            '/outbox/preview',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'simplest-test-message',
                'parameters' => [
                    'name' => 'Bob',
                    'email' => 'bob@example.com'
                ]
            ])),
            [
                'HTTP_ACCEPT' => 'text/html'
            ]
        );
        $response = $this->client->sendRequest($request);

        $expected = <<<HTML
<html>
    <body>
        <p>Hello, Bob</p>
    </body>
</html>
HTML;

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $response->getBody()->__toString());

        $this->assertCountSentMessages(0);
    }

    public function test_previewing_text_version()
    {
        $request = new Request(
            '/outbox/preview',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'simplest-test-message',
                'parameters' => [
                    'name' => 'Bob',
                    'email' => 'bob@example.com'
                ]
            ])),
            [
                'HTTP_ACCEPT' => 'text/plain'
            ]
        );
        $response = $this->client->sendRequest($request);

        $expected = "Hello, Bob";

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, $response->getBody()->__toString());

        $this->assertCountSentMessages(0);
    }

    public function test_previewing_without_specifying_type()
    {
        $request = new Request(
            '/outbox/preview',
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

        $html = <<<HTML
<html>
    <body>
        <p>Hello, Bob</p>
    </body>
</html>
HTML;
        $expected = ['text' => "Hello, Bob", 'html' => $html];

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($expected, json_decode($response->getBody()->__toString(), true));

        $this->assertCountSentMessages(0);
    }
}
