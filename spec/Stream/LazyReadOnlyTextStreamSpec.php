<?php

namespace spec\Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;
use Purist\Http\Stream\LazyReadOnlyTextStream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

require_once __DIR__ . '/StreamTestTrait.php';

class LazyReadOnlyTextStreamSpec extends ObjectBehavior
{
    use StreamTestTrait;

    function constructDefaultStream()
    {
        $this->beConstructedWith('test');
    }

    function isDefaultConstructionWritable()
    {
        return false;
    }

    function it_is_initializable()
    {
        $this->constructDefaultStream();

        $this->shouldHaveType(LazyReadOnlyTextStream::class);
        $this->shouldImplement(StreamInterface::class);
    }

    function it_will_tell_you_if_it_is_readable()
    {
        $this->constructDefaultStream();

        $this->isReadable()->shouldReturn(true);
    }

    function it_will_throw_exception_when_trying_to_read_from_a_write_only_stream()
    {
        $this->constructDefaultStream();
        // Just here to pass the test for read only stream
        $this->getContents()->shouldReturn('test');
    }

    function it_can_be_constructed_without_text()
    {
        $this->beConstructedWith();
        $this->getContents()->shouldReturn('');
    }

    function it_gets_metadata()
    {
        $this->constructDefaultStream();

        $this->getMetaData()->shouldReturn(
            [
                'mediatype' => 'text/plain',
                'base64' => false,
                'wrapper_type' => 'RFC2397',
                'stream_type' => 'RFC2397',
                'mode' => 'r',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'data://text/plain,test',
            ]
        );
        $this->getMetaData('mode')->shouldReturn('r');
    }

    function constructWriteOnlyStream()
    {
        // Skip for read only stream
    }

    function constructReadOnlyStream()
    {
        $this->constructDefaultStream();
    }
}
