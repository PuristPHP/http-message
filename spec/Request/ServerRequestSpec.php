<?php

namespace spec\Purist\Http\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Purist\Http\Request\ParsedBody\RawParsedBody;
use Purist\Http\Request\ServerRequest;
use Purist\Http\Request\UploadedFile\UploadedFiles;

class ServerRequestSpec extends ObjectBehavior
{
    function let(RequestInterface $request, UploadedFiles $uploadedFiles)
    {
        $this->beConstructedWith($request, [], [], $uploadedFiles);
    }

    function it_is_initializable(RequestInterface $request)
    {
        $this->shouldHaveType(ServerRequest::class);
        $this->shouldImplement(ServerRequestInterface::class);
    }

    function it_returns_server_parameters(RequestInterface $request, $uploadedFiles)
    {
        $this->beConstructedWith($request, ['REQUEST_URI' => '/'], [], $uploadedFiles);
        $this->getServerParams()->shouldReturn(['REQUEST_URI' => '/']);
    }

    function it_returns_cookie_parameters(RequestInterface $request, $uploadedFiles)
    {
        $this->beConstructedWith($request, [], ['test' => true], $uploadedFiles);
        $this->getCookieParams()->shouldReturn(['test' => true]);
    }

    function it_updates_cookie_parameters(RequestInterface $request, $uploadedFiles)
    {
        $this->beConstructedWith($request, [], ['test' => true], $uploadedFiles);
        $this->withCookieParams(['test' => false])
            ->callOnWrappedObject('getCookieParams')
            ->shouldReturn(['test' => false]);
    }

    function it_returns_query_parameters(RequestInterface $request, UriInterface $uri)
    {
        $uri->getQuery()->willReturn('something&somethingElse=1&another[hello]=true');
        $request->getUri()->willReturn($uri);

        $this->getQueryParams()->shouldReturn([
            'something' => '',
            'somethingElse' => '1',
            'another' => ['hello' => 'true']
        ]);
    }

    function it_updates_query_parameters(RequestInterface $request, UriInterface $uri)
    {
        $queryParams = [
            'something' => '',
            'somethingElse' => '1',
            'another' => ['hello' => 'true']
        ];

        $uri->getQuery()->willReturn(http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986));
        $uri->withQuery(Argument::any())->willReturn($uri);
        $request->withUri(Argument::any(), false)->willReturn($request);
        $request->getUri()->willReturn($uri);

        $this->withQueryParams($queryParams)
            ->callOnWrappedObject('getQueryParams')
            ->shouldReturn($queryParams);
    }

    function it_returns_uploaded_files_parameters(
        UploadedFiles $uploadedFiles,
        UploadedFileInterface $uploadedFile
    ) {
        $uploadedFiles->toArray()->willReturn(['inputName' => $uploadedFile]);
        $this->getUploadedFiles()->shouldReturn(['inputName' => $uploadedFile]);
    }

    function it_returns_new_instance_with_parsed_uploaded_files(
        UploadedFileInterface $uploadedFile
    ) {
        $this->withUploadedFiles(['changedInputName' => $uploadedFile])
            ->callOnWrappedObject('getUploadedFiles')
            ->shouldReturn(['changedInputName' => $uploadedFile]);
    }

    function it_will_throw_exeption_when_passing_invalid_uploaded_files_array(
        UploadedFileInterface $uploadedFile
    ) {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringWithUploadedFiles(['changedInputName' => Argument::not($uploadedFile)]);
    }

    function it_returns_parsed_body(RequestInterface $request, $uploadedFiles)
    {
        $this->beConstructedWith(
            $request,
            [],
            [],
            $uploadedFiles,
            new RawParsedBody(['name' => 'Nicholas Ruunu', 'status' => 1])
        );

        $request->getHeader('content-type')->willReturn(['text/plain']);

        $this->getParsedBody()->shouldReturn(['name' => 'Nicholas Ruunu', 'status' => 1]);
    }

    function it_returns_new_instance_with_parsed_body(RequestInterface $request)
    {
        $parsedBody = ['name' => 'Nicholas Ruunu', 'status' => 1];
        $request->getHeader('content-type')->willReturn(['text/plain']);

        $this
            ->withParsedBody($parsedBody)
            ->callOnWrappedObject('getParsedBody')
            ->shouldReturn($parsedBody);
    }

    function it_throw_exception_when_replacing_parsed_body_with_invalid_values()
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringWithParsedBody('invalid');
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringWithParsedBody(0);
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringWithParsedBody(true);
    }
}
