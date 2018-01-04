<?php
declare(strict_types=1);

namespace Purist\Http\Response;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class Response implements ResponseInterface
{
    private $statusCode;
    private $reasonPhrase;
    private $message;

    public function __construct(
        MessageInterface $message,
        int $statusCode = 200,
        string $reasonPhrase = ''
    ) {
        $this->message = $message;
        $this->reasonPhrase = $reasonPhrase;
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritdoc
     */
    final public function getProtocolVersion(): string
    {
        return $this->message->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    final public function withProtocolVersion($version): self
    {
        return new self(
            $this->message->withProtocolVersion($version),
            $this->statusCode,
            $this->reasonPhrase
        );
    }

    /**
     * @inheritdoc
     */
    final public function getHeaders(): array
    {
        return $this->message->getHeaders();
    }

    /**
     * @inheritdoc
     */
    final public function hasHeader($name): bool
    {
        return $this->message->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    final public function getHeader($name): array
    {
        return $this->message->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    final public function getHeaderLine($name): string
    {
        return $this->message->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    final public function withHeader($name, $value): self
    {
        return new self(
            $this->message->withHeader($name, $value),
            $this->statusCode,
            $this->reasonPhrase
        );
    }

    /**
     * @inheritdoc
     */
    final public function withAddedHeader($name, $value): self
    {
        return new self(
            $this->message->withAddedHeader($name, $value),
            $this->statusCode,
            $this->reasonPhrase
        );
    }

    /**
     * @inheritdoc
     */
    final public function withoutHeader($name): self
    {
        return new self(
            $this->message->withoutHeader($name),
            $this->statusCode,
            $this->reasonPhrase
        );
    }

    /**
     * @inheritdoc
     */
    final public function getBody(): StreamInterface
    {
        return $this->message->getBody();
    }

    /**
     * @inheritdoc
     */
    final public function withBody(StreamInterface $body): self
    {
        return $this->message->withBody($body);
    }

    /**
     * @inheritdoc
     */
    final public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @inheritdoc
     */
    final public function withStatus($code, $reasonPhrase = ''): self
    {
        return new self(
            $this->message,
            $code,
            $reasonPhrase
        );
    }

    /**
     * @inheritdoc
     */
    final public function getReasonPhrase(): string
    {
        return $this->reasonPhrase;
    }
}
