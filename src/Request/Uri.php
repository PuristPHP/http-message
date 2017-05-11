<?php
declare(strict_types=1);

namespace Purist\Http\Request;

use InvalidArgumentException;
use PhpSpec\Exception\Exception;
use Psr\Http\Message\UriInterface;

final class Uri implements UriInterface
{
    const DEFAULT_PORTS = [
        'http'  => 80,
        'https' => 443,
        'ftp' => 21,
        'gopher' => 70,
        'nntp' => 119,
        'news' => 119,
        'telnet' => 23,
        'tn3270' => 23,
        'imap' => 143,
        'pop' => 110,
        'ldap' => 389,
    ];

    private $scheme;
    private $user;
    private $password;
    private $host;
    private $port;
    private $path;
    private $query;
    private $fragment;

    public function __construct(string $uri = '')
    {
        if (false === $parsedUri = parse_url($uri)) {
            throw new Exception(
                sprintf('Could not parse string "%s"', $uri)
            );
        }

        $this->scheme = $parsedUri['scheme'] ?? '';
        $this->user = $parsedUri['user'] ?? '';
        $this->password = $parsedUri['pass'] ?? '';
        $this->host = $parsedUri['host'] ?? '';
        $this->port = $parsedUri['port'] ?? null;
        $this->path = !empty($parsedUri['path']) ? rawurldecode($parsedUri['path']) : '';
        $this->query = $parsedUri['query'] ?? '';
        $this->fragment = $parsedUri['fragment'] ?? '';
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return implode(
            [
                $this->getScheme() !== '' ? $this->getScheme() . ':' : null,
                $this->getAuthority() !== '' ? '//' . $this->getAuthority() : null,
                '/' . ltrim($this->getPath(), '/'),
                $this->getQuery() !== '' ? '?' . $this->getQuery() : null,
                $this->getFragment() !== '' ? '#' . $this->getFragment() : null,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getScheme(): string
    {
        return mb_strtolower($this->scheme);
    }

    /**
     * @inheritdoc
     */
    public function getAuthority(): string
    {
        return implode(
            ':',
            array_filter(
                [
                    implode(
                    '@',
                        array_filter(
                            [
                                $this->getUserInfo(),
                                $this->getHost(),
                            ]
                        )
                    ),
                    $this->getPort(),
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo(): string
    {
        return implode(':', array_filter([$this->user, $this->password]));
    }

    /**
     * @inheritdoc
     */
    public function getHost(): string
    {
        return mb_strtolower($this->host);
    }

    /**
     * @inheritdoc
     */
    public function getPort(): ?int
    {
        if ($this->port === null) {
            return null;
        }

        if (
            array_key_exists($this->scheme, self::DEFAULT_PORTS)
            && self::DEFAULT_PORTS[$this->scheme] === $this->port
        ) {
            return null;
        }

        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function getPath(): string
    {
        return $this->rawUrlEncode($this->path);
    }

    /**
     * @inheritdoc
     */
    public function getQuery(): string
    {
        return $this->rawUrlEncode($this->query);
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->rawUrlEncode($this->fragment);
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        $uri = clone $this;
        $uri->scheme = $scheme;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        $uri = clone $this;
        $uri->user = $user;
        $uri->password = $password;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        $uri = clone $this;
        $uri->host = $host !== null && $host !== '' ? $host : null;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withPort($port)
    {
        if ($port !== null && ($port < 1 || $port > 65535)) {
            throw new InvalidArgumentException(
                sprintf('Invalid port %d must be between 1 and 65535', $port)
            );
        }

        $uri = clone $this;
        $uri->port = $port !== null ? (int) $port : null;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        $uri = clone $this;
        $uri->path = $path;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        $uri = clone $this;
        $uri->query = $query !== null && $query !== ''
            ? ltrim($query, '?')
            : null;
        return $uri;
    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment): self
    {
        $uri = clone $this;
        $uri->fragment = $fragment !== null && $fragment !== ''
            ? ltrim($fragment, '#')
            : '';
        return $uri;
    }

    private function rawUrlEncode(string $value): string
    {
        return preg_replace_callback(
            '/(?:[^a-zA-Z0-9_\-.~!$&\'()*+,;=%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[0]);
            },
            $value
        );
    }
}
