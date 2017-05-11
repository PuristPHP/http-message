<?php
declare(strict_types=1);

namespace Purist\Http\Request;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;
use Purist\Http\Request\ParsedBody\ParsedBody;
use Purist\Http\Request\ParsedBody\RawParsedBody;
use Purist\Http\Request\UploadedFile\ProcessedUploadedFiles;
use Purist\Http\Request\UploadedFile\UploadedFiles;

final class ServerRequest implements ServerRequestInterface
{
    private $request;
    private $serverParams;
    private $cookieParams;
    private $uploadedFiles;
    private $attributes;
    private $parsedBody;

    public function __construct(
        RequestInterface $request,
        array $serverParams,
        array $cookieParams,
        UploadedFiles $uploadedFiles,
        ParsedBody $parsedBody = null,
        array $attributes = []
    ) {
        $this->request = $request;
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookieParams;
        $this->uploadedFiles = $uploadedFiles;
        $this->parsedBody = $parsedBody ?? new RawParsedBody();
        $this->attributes = $attributes;
    }

    /**
     * @inheritdoc
     */
    public function getProtocolVersion(): string
    {
        return $this->request->getProtocolVersion();
    }

    /**
     * @inheritdoc
     */
    public function withProtocolVersion($version): self
    {
        return new self(
            $this->request->withProtocolVersion($version),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getHeaders(): array
    {
        return $this->request->getHeaders();
    }

    /**
     * @inheritdoc
     */
    public function hasHeader($name): bool
    {
        return $this->request->hasHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeader($name): array
    {
        return $this->request->getHeader($name);
    }

    /**
     * @inheritdoc
     */
    public function getHeaderLine($name): string
    {
        return $this->request->getHeaderLine($name);
    }

    /**
     * @inheritdoc
     */
    public function withHeader($name, $value): self
    {
        return new self(
            $this->request->withHeader($name, $value),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function withAddedHeader($name, $value): self
    {
        return new self(
            $this->request->withAddedHeader($name, $value),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function withoutHeader($name): self
    {
        return new self(
            $this->request->withoutHeader($name),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getBody(): StreamInterface
    {
        return $this->request->getBody();
    }

    /**
     * @inheritdoc
     */
    public function withBody(StreamInterface $body): self
    {
        return new self(
            $this->request->withBody($body),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getRequestTarget(): string
    {
        return $this->request->getRequestTarget()->toString();
    }

    /**
     * @inheritdoc
     */
    public function withRequestTarget($requestTarget): self
    {
        return new self(
            $this->request->withRequestTarget($requestTarget),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getMethod(): string
    {
        return $this->request->getMethod();
    }

    /**
     * @inheritdoc
     */
    public function withMethod($method): self
    {
        return new self(
            $this->request->withMethod($method),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getUri(): UriInterface
    {
        return $this->request->getUri();
    }

    /**
     * @inheritdoc
     */
    public function withUri(UriInterface $uri, $preserveHost = false): self
    {
        return new self(
            $this->request->withUri($uri, $preserveHost),
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getServerParams(): array
    {
        return $this->serverParams;
    }

    /**
     * @inheritdoc
     */
    public function getCookieParams(): array
    {
        return $this->cookieParams;
    }

    /**
     * @inheritdoc
     */
    public function withCookieParams(array $cookies): self
    {
        return new self(
            $this->request,
            $this->serverParams,
            $cookies,
            $this->uploadedFiles,
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getQueryParams(): array
    {
        parse_str($this->request->getUri()->getQuery(), $queryParams);
        return $queryParams;
    }

    /**
     * @inheritdoc
     */
    public function withQueryParams(array $query): self
    {
        return $this->withUri(
            $this->getUri()->withQuery(
                http_build_query($query, '', '&', PHP_QUERY_RFC3986)
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getUploadedFiles(): array
    {
        return $this->uploadedFiles->toArray();
    }

    /**
     * @inheritdoc
     */
    public function withUploadedFiles(array $uploadedFiles): self
    {
        return new self(
            $this->request,
            $this->serverParams,
            $this->cookieParams,
            new ProcessedUploadedFiles($uploadedFiles),
            $this->parsedBody,
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getParsedBody()
    {
        return $this->parsedBody->parse(
            $this->getHeader('content-type'),
            $this->request->getBody()
        );
    }

    /**
     * @inheritdoc
     */
    public function withParsedBody($data): self
    {
        return new self(
            $this->request,
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            new RawParsedBody($data),
            $this->attributes
        );
    }

    /**
     * @inheritdoc
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @inheritdoc
     */
    public function getAttribute($name, $default = null)
    {
        if (!array_key_exists($name, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$name];
    }

    /**
     * @inheritdoc
     */
    public function withAttribute($name, $value): self
    {
        return new self(
            $this->request,
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            array_merge($this->attributes, [$name => $value])
        );
    }

    /**
     * @inheritdoc
     */
    public function withoutAttribute($name): self
    {
        return new self(
            $this->request,
            $this->serverParams,
            $this->cookieParams,
            $this->uploadedFiles,
            $this->parsedBody,
            array_filter(
                $this->attributes,
                function ($key) use ($name) {
                    return mb_strtolower($key) !== mb_strtolower($name);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }
}
