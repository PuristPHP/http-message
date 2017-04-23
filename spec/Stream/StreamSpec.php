<?php

namespace spec\Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;
use Purist\Http\Stream\Stream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__ . '/StreamTestTrait.php';

class StreamSpec extends ObjectBehavior
{
    use StreamTestTrait;

    function constructDefaultStream() {
        $this->resource = fopen('php://temp', 'r+');
        fwrite($this->resource, 'test');
        rewind($this->resource);
        $this->beConstructedWith($this->resource);
    }

    function isDefaultConstructionWritable()
    {
        return true;
    }

    function it_is_initializable()
    {
        $this->constructDefaultStream();

        $this->shouldHaveType(Stream::class);
        $this->shouldImplement(StreamInterface::class);
    }

    function it_should_throw_exception_when_not_constructed_with_resource()
    {
        $this->beConstructedWith('invalid');
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function constructWriteOnlyStream()
    {
        $this->beConstructedWith(
            $resource = fopen('php://stdout', 'w')
        );
    }

    function constructReadOnlyStream()
    {
        $this->beConstructedWith(
            fopen('php://temp', 'r')
        );
    }
}
