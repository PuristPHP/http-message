<?php
declare(strict_types=1);

namespace Purist\Http\Response;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Purist\Http\Header\Headers;
use Purist\Http\Message;
use Purist\Http\Stream\LazyReadOnlyTextStream;

final class TextResponse implements ResponseInterface
{
    private $response;

    public function __construct(string $text, int $statusCode = 200, Headers $headers = null)
    {
        $this->response = new Response(
            new Message(new LazyReadOnlyTextStream($text), $headers),
            $statusCode
        );
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion()
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version)
    {
        return $this->response->withProtocolVersion($version);
    }

    /**
     * @inheritdoc
     */
    public function getHeaders()
    {
        return $this->response->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name)
    {
        return $this->response->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name)
    {
        return $this->response->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name)
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value)
    {
        return $this->response->withHeader($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value)
    {
        return $this->response->withAddedHeader($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name)
    {
        return $this->response->withoutHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body)
    {
        return $this->response->withBody($body);
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        return $this->response->withStatus($code, $reasonPhrase);
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase()
    {
        return $this->response->getReasonPhrase();
    }
}
