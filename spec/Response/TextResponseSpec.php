<?php

namespace spec\Purist\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Purist\Http\Header\HttpHeaders;
use Purist\Http\Response\TextResponse;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class TextResponseSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            'text',
            403,
            new HttpHeaders(['X-Header' => 'value'])
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TextResponse::class);
        $this->shouldImplement(ResponseInterface::class);
    }

    function it_gets_body()
    {
        $this->getBody()->callOnWrappedObject('getContents')->shouldReturn('text');
    }

    function it_gets_headers()
    {
        $this->getHeaders()->shouldReturn(['X-Header' => ['value']]);
    }

    function it_gets_status_code()
    {
        $this->getStatusCode()->shouldReturn(403);
    }
}
