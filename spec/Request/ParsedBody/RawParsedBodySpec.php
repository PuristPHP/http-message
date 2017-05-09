<?php

namespace spec\Purist\Http\Request\ParsedBody;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\StreamInterface;
use Purist\Http\Request\ParsedBody\ParsedBody;
use Purist\Http\Request\ParsedBody\RawParsedBody;

class RawParsedBodySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['test']);
    }

    function it_is_initializable()
    {
        $this->shouldBeAnInstanceOf(RawParsedBody::class);
        $this->shouldImplement(ParsedBody::class);
    }

    function it_returns_parsed_body(StreamInterface $stream)
    {
        $this->parse(['text/plain'], $stream)->shouldReturn(['test']);
    }

    function it_throws_exception_on_invalid_values()
    {
        $this->beConstructedWith('string');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
