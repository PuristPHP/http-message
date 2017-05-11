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
    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): self
    {
        $response = clone $this;
        $response->response = $this->response->withProtocolVersion($version);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name): bool
    {
        return $this->response->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name): array
    {
        return $this->response->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name): string
    {
        return $this->response->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): self
    {
        $response = clone $this;
        $response->response = $this->response->withHeader($name, $value);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value): self
    {
        $response = clone $this;
        $response->response = $this->response->withAddedHeader($name, $value);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name): self
    {
        $response = clone $this;
        $response->response = $this->response->withoutHeader($name);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body)
    {
        $response = clone $this;
        $response->response = $this->response->withBody($body);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * @inheritdoc
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $response = clone $this;
        $response->response = $this->response->withStatus($code, $reasonPhrase);
        return $response;
    }

    /**
     * @inheritdoc
     */
    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }
}
