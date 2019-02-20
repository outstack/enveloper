<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Outstack\Enveloper\Infrastructure\Delivery\DeliveryMethod\SwiftMailer\SwiftMailerInterface;
use Zend\Diactoros\Request;

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

    public function test_asking_for_unavailable_type_fails()
    {
        $request = new Request(
            '/outbox/preview',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'without-text-version',
                'parameters' => [
                    'name' => 'Bob',
                    'email' => 'bob@example.com'
                ]
            ])),
            [
                'HTTP_ACCEPT' => 'text/plain'
            ]
        );
        try {
            $this->client->sendRequest($request);
        } catch (HttpException $exception) {
            $this->assertEquals(406, $exception->getResponse()->getStatusCode());
            $this->assertEquals(
                [
                    'title' => 'Not Acceptable',
                    'status' => 406,
                    'detail' => 'No version of this email matching your Accept header could be found',
                    'availableContentTypes' => [
                        'application/json',
                        'text/html'
                    ]
                ],
                json_decode($exception->getResponse()->getBody(), true));
            return;
        }

        throw new \LogicException("Expected HTTP exception but did not find one");
    }
}
