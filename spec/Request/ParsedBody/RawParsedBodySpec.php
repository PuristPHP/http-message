<?php

namespace spec\Purist\Http\Request\ParsedBody;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
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

    function it_returns_parsed_body()
    {
        $data = (object) ['test'];
        $this->beConstructedWith($data);
        $this->get('text/plain')->shouldReturn($data);
    }

    function it_throws_exception_on_other_values()
    {
        $this->beConstructedWith('string');
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }

    function it_can_replace_parsed_body()
    {
        $this
            ->withParsedBody($data = ['test'])
            ->callOnWrappedObject('get', ['text/plain'])
            ->shouldReturn($data);

        $this
            ->withParsedBody($data =(object) ['test'])
            ->callOnWrappedObject('get', ['text/plain'])
            ->shouldReturn($data);
    }

    function it_will_throw_exception_when_replacing_with_invalid_values()
    {
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->duringWithParsedBody(1);
        $this
            ->shouldThrow(InvalidArgumentException::class)
            ->duringWithParsedBody('test');
    }
}
