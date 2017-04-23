<?php

namespace spec\Purist\Request;

use InvalidArgumentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParsedBodySpec extends ObjectBehavior
{
    function it_accepts_array()
    {
        $this->beConstructedWith(['test']);
        $this->get()->shouldReturn(['test']);
    }

    function it_accepts_std_object()
    {
        $data = (object) ['test'];
        $this->beConstructedWith($data);
        $this->get()->shouldReturn($data);
    }

    function it_throws_exception_on_other_values()
    {
        $this->beConstructedWith("string");
        $this->shouldThrow(InvalidArgumentException::class)->duringInstantiation();
    }
}
