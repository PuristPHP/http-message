<?php

namespace spec\Purist\Http\Request\UploadedFile;

use phpmock\Mock;
use phpmock\prophecy\PHPProphet;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Purist\Http\Request\UploadedFile\UploadedFile;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UploadedFileSpec extends ObjectBehavior
{
    function let()
    {
        // Mock is_uploaded_file since it's not a real request
        $prophet = new PHPProphet();
        $prophecy = $prophet->prophesize('Purist\\Http\\Request\\UploadedFile');
        $prophecy->is_uploaded_file('/tmp/SAkakekA')->willReturn(true);
        $prophecy->move_uploaded_file('/tmp/SAkakekA', '/tmp/destination')->willReturn(true);
        $prophecy->reveal();

        $this->beConstructedWith(
            'index.html',
            'text/html',
            500,
            '/tmp/SAkakekA',
            UPLOAD_ERR_OK
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UploadedFile::class);
        $this->shouldImplement(UploadedFileInterface::class);
    }

    function it_returns_stream_of_uploaded_file()
    {
        $this->getStream()->shouldBeAnInstanceOf(StreamInterface::class);
    }

    function it_moves_uploaded_file()
    {
        $this->shouldNotThrow()->duringMoveTo('/tmp/destination');
    }

    function it_gets_size_when_available()
    {
        $this->getSize()->shouldReturn(500);
    }

    function it_gets_the_file_name_from_client()
    {
        $this->getClientFilename()->shouldReturn('index.html');
    }

    function it_gets_the_media_type()
    {
        $this->getClientMediaType()->shouldReturn('text/html');
    }

    function letGo()
    {
        Mock::disableAll();
    }
}
