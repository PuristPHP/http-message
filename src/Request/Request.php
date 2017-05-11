<?php
declare(strict_types=1);

namespace Purist\Http\Request;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Purist\Http\Message;
use Purist\Http\Stream\LazyReadOnlyTextStream;

final class Request implements RequestInterface
{
    private $message;
    private $uri;
    private $method;
    private $requestTarget;

    public function __construct(
        UriInterface $uri,
        MessageInterface $message = null,
        string $method = 'GET',
        string $requestTarget = RequestTarget::ORIGIN_FORM
    ) {
        $this->uri = $uri;
        $this->message = $message ?? new Message(new LazyReadOnlyTextStream());
        $this->method = $method;
        $this->requestTarget = new RequestTarget($uri, $requestTarget);
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion(): string
    {
        return $this->message->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): self
    {
        return new self(
            $this->uri,
            $this->message->withProtocolVersion($version),
            $this->method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->message->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name): bool
    {
        return $this->message->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name): array
    {
        return $this->message->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name): string
    {
        return $this->message->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): self
    {
        return new self(
            $this->uri,
            $this->message->withHeader($name, $value),
            $this->method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value): self
    {
        return new self(
            $this->uri,
            $this->message->withAddedHeader($name, $value),
            $this->method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name): self
    {
        return new self(
            $this->uri,
            $this->message->withoutHeader($name),
            $this->method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function getBody(): StreamInterface
    {
        return $this->message->getBody();
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body): self
    {
        return new self(
            $this->uri,
            $this->message->withBody($body),
            $this->method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function getRequestTarget(): string
    {
        return $this->requestTarget->toString();
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget): self
    {
        return new self(
            $this->uri,
            $this->message,
            $this->method,
            $requestTarget
        );
    }

    /**
     * @inheritdoc
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method): self
    {
        return new self(
            $this->uri,
            $this->message,
            $method,
            $this->requestTarget->form()
        );
    }

    /**
     * @inheritdoc
     */
    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        return new self(
            $preserveHost === true ? $uri->withHost($this->uri->getHost()) : $uri,
            $this->message,
            $this->method,
            $this->requestTarget->form()
        );
    }
}
