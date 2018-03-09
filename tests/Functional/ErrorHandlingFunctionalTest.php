<?php

namespace Outstack\Enveloper\Tests\Functional;

use Http\Client\Exception\HttpException;
use Outstack\Components\SymfonySwiftMailerAssertionLibrary\SwiftMailerAssertionTrait;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Zend\Diactoros\Request;
use Zend\Diactoros\Stream;
use Zend\Diactoros\Uri;

class ErrorHandlingFunctionalTest extends AbstractApiTestCase
{
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

    public function test_server_error_is_nicely_formatted()
    {
        $request = new Request(
            '/errors/500',
            'GET'
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(500, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Server Error',
                'detail' => 'An unexpected error occurred',
                'status' => 500
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");
    }

    public function test_syntax_error_is_nicely_formatted()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template-typo' => 'message-with-attachments',
                'parameters' => [
                    'email' => 'bob@example.com',
                    'attachments' => [
                        ['contents' => 'This is a note', 'filename' => 'note.txt']
                    ]
                ]
            ]))
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(400, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Syntax Error',
                'detail' => 'Request failed JSON schema validation',
                'status' => 400,
                'errors' => [
                    [
                        'error' => 'The object must contain the properties ["template"].',
                        'path' => '/required'
                    ]
                ]
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");

    }

    public function test_parameters_sent_to_template_are_validated_by_schema()
    {
        $request = new Request(
            '/outbox',
            'POST',
            $this->convertToStream(json_encode([
                'template' => 'message-with-attachments',
                'parameters' => [
                    'email' => 'bob@example.com',
                    'attachments' => [
                        ['contents' => 'This is a note', 'filename' => '']
                    ]
                ]
            ]))
        );

        try {
            $this->client->sendRequest($request);
        } catch (HttpException $e) {

            $response   = $e->getResponse();
            $body       = (string) $response->getBody();

            $this->assertEquals(400, $response->getStatusCode());
            $this->assertJson($body);
            $this->assertEquals([
                'title' => 'Parameters failed JSON schema validation',
                'detail' => 'A template was found but the parameters submitted to it do not validate against the configured JSON schema',
                'status' => 400,
                'errors' => [
                    [
                        'error' => 'The string must be at least 1 characters long.',
                        'path' => '/properties/attachments/items/0/properties/filename/minLength'
                    ]
                ]
            ], json_decode($body, true));
            $this->assertEquals('application/problem+json', $response->getHeaderLine('Content-type'));

            return;
        }

        throw new \LogicException("Expected HttpException, none caught");

    }
}