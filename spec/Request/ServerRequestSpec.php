<?php

namespace spec\Purist\Request;

use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Stream;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;
use Purist\Message\HttpHeaders;
use Purist\Message\HttpMessage;
use Purist\Request\ParsedBody;
use Purist\Request\Request;
use Purist\Request\UploadedFile\UploadedFile;
use Purist\Request\Uri;

class ServerRequestSpec extends ObjectBehavior
{
    function let(RequestInterface $request)
    {
        $this->beConstructedWith($request, [], [], []);
    }

    function it_is_initializable(RequestInterface $request)
    {
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

    function it_returns_parsed_body(RequestInterface $request)
    {
        $this->beConstructedWith(
            $request,
            [],
            [],
            [],
            new ParsedBody(['name' => 'Nicholas Ruunu', 'status' => 1])
        );

        $this->getParsedBody()->shouldReturn(['name' => 'Nicholas Ruunu', 'status' => 1]);
    }

    function it_returns_new_instance_with_parsed_body()
    {
        $parsedBody = ['name' => 'Nicholas Ruunu', 'status' => 1];

        $this
            ->withParsedBody($parsedBody)
            ->callOnWrappedObject('getParsedBody')
            ->shouldReturn($parsedBody);
    }

    function it_constructs_from_globals()
    {
        $_SERVER['REQUEST_URI'] = '/path';
        $_POST['text'] = 'hello';

       $this->beConstructedThrough('fromGlobals');
       $this->getUri()->callOnWrappedObject('getPath')->shouldReturn('/path');
       $this->getParsedBody()->shouldReturn(['text' => 'hello']);
    }
}
