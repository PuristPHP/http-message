<?php

namespace spec\Purist\Response;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Purist\Response\Response;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use spec\Purist\Message\HttpMessageSpec;

class ResponseSpec extends ObjectBehavior
{
    function let(MessageInterface $message)
    {
        $this->beConstructedWith(200, 'Reasonphrase', $message);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Response::class);
        $this->shouldImplement(ResponseInterface::class);
    }

    function it_returns_the_http_status_code()
    {
        $this->getStatusCode()->shouldReturn(200);
    }

    function it_returns_new_instance_with_changed_status()
    {
        $response = $this->withStatus(404, 'not found');
        $response->callOnWrappedObject('getStatusCode')->shouldReturn(404);
        $response->callOnWrappedObject('getReasonPhrase')->shouldReturn('not found');
    }

    function it_gets_the_protocol_version($message)
    {
        $this->getProtocolVersion();
        $message->getProtocolVersion()->shouldHaveBeenCalled();
    }

    function it_returns_a_new_instance_with_updated_protocol_version($message)
    {
        $message->getProtocolVersion()->willReturn('1.0');
        $message->withProtocolVersion('1.0')->shouldBeCalled()->willReturn($message);
        $this->withProtocolVersion('1.0')
            ->callOnWrappedObject('getProtocolVersion')
            ->shouldReturn('1.0');
    }

    function it_returns_headers($message)
    {
        $message->getHeaders()->willReturn(['Content-Type' => 'text/html']);
        $this->getHeaders()->shouldReturn(['Content-Type' => 'text/html']);
    }

    function it_checks_if_header_exists($message)
    {
        $message->hasHeader('Content-Type')->willReturn(true);
        $message->hasHeader('Content-Encoding')->willReturn(false);

        $this->hasHeader('Content-Type')->shouldReturn(true);
        $this->hasHeader('Content-Encoding')->shouldReturn(false);
    }

    function it_returns_header_values($message)
    {
        $message->getHeader('Content-Type')->willReturn(['text/html']);
        $this->getHeader('Content-Type')->shouldReturn(['text/html']);
    }
}
