<?php

namespace spec\Purist\Http\Stream;

use Purist\Http\Stream\MemoizedStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Stream\Stream;

class MemoizedStreamSpec extends ObjectBehavior
{
    private $stream;

    function let()
    {
        $this->stream = new Stream(fopen('data://text/plain,test', 'r'));

        $this->beConstructedWith(function() {
            return $this->stream;
        });
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MemoizedStream::class);
    }

    function it_returns_the_callables_return_value()
    {
        $this->get()->shouldReturn($this->stream);
    }
}
