<?php

namespace spec\Purist\Http\Request;

use Purist\Http\Request\GlobalServerRequest;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\ServerRequest;

class GlobalServerRequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(GlobalServerRequest::class);
    }

    function it_creates_server_request_from_globals()
    {
        $_SERVER['HTTPS'] = true;
        $_SERVER['HTTP_HOST'] = 'localhost:1337';
        $_SERVER['HTTP_CONTENT_TYPE'] = 'text/html';
        $_SERVER['REQUEST_URI'] = '/path?something=true&is=awesome#anchor';
        $_SERVER['REQUEST_METHOD'] = 'pOsT';
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $_POST['text'] = 'hello';

        $serverRequest = $this->create();
        $serverRequest->shouldBeAnInstanceOf(ServerRequest::class);

        $uri = $serverRequest->callOnWrappedObject('getUri');
        $uri->callOnWrappedObject('getScheme')->shouldReturn('https');
        $uri->callOnWrappedObject('getHost')->shouldReturn('localhost');
        $uri->callOnWrappedObject('getPort')->shouldReturn(1337);
        $uri->callOnWrappedObject('getPath')->shouldReturn('/path');
        $uri->callOnWrappedObject('getQuery')->shouldReturn('something=true&is=awesome');
        $uri->callOnWrappedObject('getFragment')->shouldReturn('anchor');

        $serverRequest
            ->callOnWrappedObject('getParsedBody')
            ->shouldReturn(['text' => 'hello']);

        $serverRequest->getHeader('content-type')->shouldReturn(['text/html']);
        $serverRequest->getHeader('host')->shouldReturn(['localhost:1337']);
        $serverRequest->getMethod()->shouldReturn('pOsT');
        $serverRequest->getProtocolVersion()->shouldReturn('1.0');
    }
}
