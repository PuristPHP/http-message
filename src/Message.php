<?php
declare(strict_types=1);

namespace Purist\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Purist\Http\Header\Headers;
use Purist\Http\Header\HttpHeaders;

final class Message implements MessageInterface
{
    private $protocolVersion;
    private $headers;
    private $body;

    public function __construct(
        StreamInterface $body,
        Headers $headers = null,
        string $protocolVersion = '1.1'
    ) {
        $this->body = $body;
        $this->headers = $headers ?? new HttpHeaders();
        $this->protocolVersion = $protocolVersion;
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): self
    {
        return new self($this->body, $this->headers, $version);
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->headers->toArray();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name): bool
    {
        return $this->headers->has($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name): array
    {
        return $this->headers->header($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name): string
    {
        return $this->headers->headerLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): self
    {
        return new self(
            $this->body,
            $this->headers->replace($name, $value),
            $this->protocolVersion
        );
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value): self
    {
        return new self(
            $this->body,
            $this->headers->add($name, $value),
            $this->protocolVersion
        );
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name): self
    {
        return new self(
            $this->body,
            $this->headers->remove($name),
            $this->protocolVersion
        );
    }

    /**
     * @inheritdoc
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body): self
    {
        return new self($body, $this->headers, $this->protocolVersion);
    }
}
