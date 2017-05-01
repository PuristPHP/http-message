<?php
declare(strict_types=1);

namespace Purist\Http\Request;

use Purist\Http\Header\HttpHeaders;
use Purist\Http\Message;
use Purist\Http\Request\ParsedBody\RawParsedBody;
use Purist\Http\Request\UploadedFile\RawUploadedFiles;
use Purist\Http\Stream\LazyStream;

final class GlobalServerRequest
{
    public function create(): ServerRequest
    {
        return new ServerRequest(
            new Request(
                $this->createUriFromGlobals(),
                new Message(
                    new LazyStream('php://input', 'r'),
                    new HttpHeaders(
                        function_exists('getallheaders') ? getallheaders() : []
                    ),
                    str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL'] ?? '1.1')
                ),
                $_SERVER['REQUEST_METHOD'] ?? 'GET'
            ),
            $_SERVER,
            $_COOKIE,
            new RawUploadedFiles($_FILES),
            new RawParsedBody($_POST ?? null)
        );
    }

    private function createUriFromGlobals(): Uri
    {
        @list($host, $port) = explode(
            ':',
            $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? $_SERVER['SERVER_ADDR'] ?? ''
        );
        @list($path, $query) = explode('?', $_SERVER['REQUEST_URI'] ?? '');
        @list($query, $fragment) = explode('#', $query ?? '');

        return (new Uri())
            ->withScheme(
                !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
                    ? 'https'
                    : 'http'
            )
            ->withHost($host)
            ->withPort($port ?? $_SERVER['SERVER_PORT'] ?? 80)
            ->withPath($path !== '' ? $path : null)
            ->withQuery($query !== '' ? $query : $_SERVER['QUERY_STRING'] ?? null)
            ->withFragment($fragment);
    }
}
