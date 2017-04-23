<?php

namespace spec\Purist\Http\Stream;

use Prophecy\Argument;
use RuntimeException;

trait StreamTestTrait
{
    private $resource;

    abstract function constructWriteOnlyStream();
    abstract function constructReadOnlyStream();
    abstract function constructDefaultStream();
    abstract function isDefaultConstructionWritable();

    function it_should_return_rewinded_stream_when_cast_to_string()
    {
        $this->constructDefaultStream();

        $this->seek(0, SEEK_END);
        $this->__toString()->shouldReturn('test');
    }

    function it_should_not_return_string_when_closed()
    {
        $this->constructDefaultStream();

        $this->close();
        @$this->__toString()->shouldReturn('');
    }

    function it_can_detach_resource_from_stream()
    {
        $this->constructDefaultStream();

        $resource = $this->detach()->getWrappedObject();

        if (!is_resource($resource)) {
            throw new \Exception('Method detach must return resource.');
        }

        $this->detach()->shouldReturn(null);
    }

    function it_returns_the_size_of_the_steam()
    {
        $this->constructDefaultStream();

        $this->getSize()->shouldReturn(4);
    }

    function it_tells_stream_position()
    {
        $this->constructDefaultStream();

        $this->seek(4);
        $this->tell()->shouldReturn(4);

        $this->seek(2);
        $this->tell()->shouldReturn(2);
    }

    function it_throws_exception_when_unable_to_tell_position()
    {
        $this->constructDefaultStream();

        $this->detach();
        @$this->shouldThrow(RuntimeException::class)->duringTell();
    }

    function it_returns_end_of_file_boolean()
    {
        $this->constructDefaultStream();

        $this->eof()->shouldReturn(false);

        $this->getContents();
        $this->eof()->shouldReturn(true);
    }

    function it_returns_seekable_boolean()
    {
        $this->constructDefaultStream();

        $this->isSeekable()->shouldReturn(true);

        $this->detach();
        @$this->isSeekable()->shouldReturn(false);
    }

    function it_seeks_on_stream()
    {
        $this->constructDefaultStream();

        $this->seek(4);
        $this->tell()->shouldReturn(4);

        $this->seek(2);
        $this->tell()->shouldReturn(2);
    }

    function it_throws_exception_when_unable_to_seek()
    {
        $this->constructDefaultStream();

        $this->detach();
        @$this->shouldThrow(RuntimeException::class)->duringSeek(2);
    }

    function it_rewinds_stream()
    {
        $this->constructDefaultStream();

        $this->seek(4);
        $this->rewind();

        $this->tell()->shouldReturn(0);
    }

    function it_throws_exception_when_rewinding_not_seekable_stream()
    {
        $this->constructDefaultStream();
        $this->detach();
        @$this->shouldThrow(RuntimeException::class)->duringRewind();
    }

    function it_returns_if_its_writable()
    {
        $this->constructDefaultStream();

        $this->isWritable()->shouldReturn($this->isDefaultConstructionWritable());
    }


    function it_can_return_content_as_string()
    {
        $this->constructDefaultStream();

        $this->getContents()->shouldReturn('test');
    }

    function it_gets_metadata()
    {
        $this->constructDefaultStream();

        $this->getMetaData()->shouldReturn(
            [
                'wrapper_type' => 'PHP',
                'stream_type' => 'TEMP',
                'mode' => 'w+b',
                'unread_bytes' => 0,
                'seekable' => true,
                'uri' => 'php://temp',
            ]
        );
        $this->getMetaData('mode')->shouldReturn('w+b');
    }

    function it_can_read_from_stream()
    {
        $this->constructDefaultStream();

        $this->read(3)->shouldReturn('tes');
    }

    function it_writes_data_to_writable_streams()
    {
        $this->constructDefaultStream();

        if ($this->isDefaultConstructionWritable()) {
            $this->write('test-new')->shouldReturn(8);
        }
    }

    function it_returns_false_when_stream_is_detached()
    {
        $this->constructReadOnlyStream();

        $this->detach();
        @$this->isWritable()->shouldReturn(false);
    }

    function it_will_throw_exception_when_trying_to_write_to_read_only_stream()
    {
        $this->constructReadOnlyStream();

        $this->shouldThrow(RuntimeException::class)->duringWrite('invalid');
    }

    function it_will_tell_you_if_it_is_readable()
    {
        $this->constructWriteOnlyStream();

        $this->isReadable()->shouldReturn(false);
    }

    function it_will_throw_exception_when_trying_to_read_from_a_write_only_stream()
    {
        $this->constructWriteOnlyStream();

        $this->shouldThrow(RuntimeException::class)->duringRead(1);
    }
}
