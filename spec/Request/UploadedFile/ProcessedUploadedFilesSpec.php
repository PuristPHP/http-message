<?php

namespace spec\Purist\Http\Request\UploadedFile;

use Psr\Http\Message\UploadedFileInterface;
use Purist\Http\Request\UploadedFile\ProcessedUploadedFiles;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\UploadedFile\UploadedFiles;

class ProcessedUploadedFilesSpec extends ObjectBehavior
{
    function it_is_initializable(UploadedFileInterface $uploadedFile)
    {
        $this->beConstructedWith(['inputName' => $uploadedFile]);
        $this->shouldHaveType(ProcessedUploadedFiles::class);
        $this->shouldImplement(UploadedFiles::class);
    }

    function it_can_be_constructed_with_nested_input_names(UploadedFileInterface $uploadedFile)
    {
        $this->beConstructedWith(['inputName' => ['anotherLevel' => $uploadedFile]]);
        $this->shouldNotThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_can_be_constructed_with_multiple_files_in_input(UploadedFileInterface $uploadedFile)
    {
        $this->beConstructedWith(['inputName' => [$uploadedFile, $uploadedFile]]);
        $this->shouldNotThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_will_throw_exception_when_constructed_invalid(UploadedFileInterface $uploadedFile)
    {
        $this->beConstructedWith(['inputName' => Argument::not($uploadedFile)]);
        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }
}
