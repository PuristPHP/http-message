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
        $_SERVER['REQUEST_URI'] = '/path';
        $_POST['text'] = 'hello';

        $serverRequest = $this->create();
        $serverRequest->shouldBeAnInstanceOf(ServerRequest::class);
        $serverRequest
            ->callOnWrappedObject('getUri')
            ->callOnWrappedObject('getPath')
            ->shouldReturn('/path');
        $serverRequest
            ->callOnWrappedObject('getParsedBody')
            ->shouldReturn(['text' => 'hello']);

    }
}
