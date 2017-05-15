<?php

namespace Outstack\Components\ReactPhpSymfonyBridge\Tests\HttpFoundation;

use Outstack\Components\ReactPhpSymfonyBridge\HttpFoundation\HttpFoundationRequestAdapter;
use React\Http\Request;

class HttpFoundationRequestAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function test_method_is_preserved()
    {
        $sut = new HttpFoundationRequestAdapter();

        $request = new Request()


        $this->assertEquals('GET', $sut->convertToHttpFoundationRequest())
    }
}