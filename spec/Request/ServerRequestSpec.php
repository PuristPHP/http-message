<?php

namespace spec\Purist\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Purist\Request\UploadedFile\UploadedFile;

class ServerRequestSpec extends ObjectBehavior
{
    function it_is_initializable(RequestInterface $request)
    {
        $this->beConstructedWith($request, [], [], []);
        $this->shouldHaveType('Purist\Request\ServerRequest');
    }

    function it_returns_server_parameters(RequestInterface $request)
    {
        $this->beConstructedWith($request, ['REQUEST_URI' => '/'], [], []);
        $this->getServerParams()->shouldReturn(['REQUEST_URI' => '/']);
    }

    function it_returns_cookie_parameters(RequestInterface $request)
    {
        $this->beConstructedWith($request, [], ['test' => true], []);
        $this->getCookieParams()->shouldReturn(['test' => true]);
    }

    function it_updates_cookie_parameters(RequestInterface $request)
    {
        $this->beConstructedWith($request, [], ['test' => true], []);
        $this->withCookieParams(['test' => false])
            ->callOnWrappedObject('getCookieParams')
            ->shouldReturn(['test' => false]);
    }

    function it_returns_query_parameters(RequestInterface $request, UriInterface $uri)
    {
        $uri->getQuery()->willReturn('something&somethingElse=1&another[hello]=true');
        $request->getUri()->willReturn($uri);
        $this->beConstructedWith($request, [], [], []);
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

        $this->beConstructedWith($request, [], [], []);
        $this->withQueryParams($queryParams)
            ->callOnWrappedObject('getQueryParams')
            ->shouldReturn($queryParams);
    }

    function it_returns_uploaded_files_parameters(RequestInterface $request)
    {
        $this->beConstructedWith(
            $request,
            [],
            [],
            [
                'inputName' => [
                    'name' => 'index.html',
                    'type' => 'text/html',
                    'size' => '500',
                    'tmp_name' => '/tmp/SAkakekA',
                    'error' => UPLOAD_ERR_OK,
                ],
            ]
        );

        $files = $this->getUploadedFiles();
        $files['inputName']
            ->callOnWrappedObject('getClientFilename')
            ->shouldReturn('index.html');

        $files['inputName']
            ->callOnWrappedObject('getClientMediaType')
            ->shouldReturn('text/html');

        $files['inputName']
            ->callOnWrappedObject('getSize')
            ->shouldReturn(500);

        $files['inputName']
            ->callOnWrappedObject('getError')
            ->shouldReturn(UPLOAD_ERR_OK);

        $files['inputName']
            ->callOnWrappedObject('getStream')
            ->shouldImplement('Psr\Http\Message\StreamInterface');
    }
}
