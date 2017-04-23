<?php

namespace spec\Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;
use Purist\Http\Stream\LazyStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__ . '/StreamTestTrait.php';

class LazyStreamSpec extends ObjectBehavior
{
    use StreamTestTrait;

    function constructDefaultStream()
    {
        $this->beConstructedWith('php://temp', 'r+');
        $this->write('test');
        $this->rewind();
    }

    function isDefaultConstructionWritable()
    {
        return true;
    }

    function it_is_initializable()
    {
        $this->constructDefaultStream();

        $this->shouldHaveType(LazyStream::class);
        $this->shouldImplement(StreamInterface::class);
    }

    function constructReadOnlyStream()
    {
        $this->beConstructedWith('php://temp', 'r');
    }

    function constructWriteOnlyStream()
    {
        $this->beConstructedWith('php://stdout', 'w');
    }
}
