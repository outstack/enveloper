<?php

namespace Outstack\Components\ReactPhpSymfonyBridge\HttpFoundation;

use React\Http\Request;

class HttpFoundationRequestAdapter
{
    public function convertToHttpFoundationRequest(Request $request): \Symfony\Component\HttpFoundation\Request
    {
        $method = $request->getMethod();
        $headers = $request->getHeaders();
        $query = $request->getQuery();
        $content = $request->getBody();
        $post = array();
        if (in_array(strtoupper($method), array('POST', 'PUT', 'DELETE', 'PATCH')) &&
            isset($headers['Content-Type']) && (0 === strpos($headers['Content-Type'], 'application/x-www-form-urlencoded'))
        ) {
            parse_str($content, $post);
        }
        $sfRequest = new \Symfony\Component\HttpFoundation\Request(
            $query,
            $post,
            array(),
            array(), // To get the cookies, we'll need to parse the headers
            $request->getFiles(),
            array(), // Server is partially filled a few lines below
            $content
        );
        $sfRequest->setMethod($method);
        $sfRequest->headers->replace($headers);
        $sfRequest->server->set('REQUEST_URI', $request->getPath());
        if (isset($headers['Host'])) {
            $sfRequest->server->set('SERVER_NAME', explode(':', $headers['Host'])[0]);
        }
    }
}