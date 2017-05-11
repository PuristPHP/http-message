<?php
declare(strict_types=1);

namespace Purist\Http\Stream;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

final class Stream implements StreamInterface
{
    private $resource;

    /**
     * @param resource $resource
     */
    public function __construct($resource)
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Stream needs to be constructed with resource, %s passed',
                    gettype($resource)
                )
            );
        }

        $this->resource = $resource;
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        rewind($this->resource);
        return (string) stream_get_contents($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
        fclose($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        return fstat($this->resource)['size'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function tell(): int
    {
        $this->assertResource();

        if (false === $position = ftell($this->resource)) {
            throw new RuntimeException('Could not get position of stream');
        }

        return $position;
    }

    /**
     * @inheritdoc
     */
    public function eof(): bool
    {
        return $this->resource === null || feof($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function isSeekable(): bool
    {
        return is_resource($this->resource) && (
            stream_get_meta_data($this->resource)['seekable'] ?? false
        );
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->assertResource();

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new RuntimeException('Failed to seek stream.');
        }
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->assertResource();

        if (!$this->isSeekable()) {
            throw new RuntimeException('Could not rewind not seekable stream.');
        }

        rewind($this->resource);
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        return $this->streamModeMatches('(r\+|w|a|x|c)');
    }

    /**
     * @inheritdoc
     */
    public function write($string): int
    {
        $this->assertResource();

        if (!$this->isWritable()) {
            throw new RuntimeException(
                sprintf(
                    'Stream with mode %s is not writable',
                    stream_get_meta_data($this->resource)['mode']
                )
            );
        }

        if (false === $bytes = fwrite($this->resource, $string)) {
            throw new RuntimeException('Failed to write to stream.');
        }

        return $bytes;
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return $this->streamModeMatches('(r\+?|(w|c|a)\+)');
    }

    /**
     * @inheritdoc
     */
    public function read($length): string
    {
        $this->assertResource();

        if (!$this->isReadable()) {
            throw new RuntimeException(
                sprintf(
                    'Stream with mode %s is not readable',
                    $this->getMetadata('mode')
                )
            );
        }

        return (string) fread($this->resource, $length);
    }

    /**
     * @inheritdoc
     */
    public function getContents(): string
    {
        $this->assertResource();

        if (false === $contents = stream_get_contents($this->resource)) {
            throw new RuntimeException(
                sprintf(
                    'Could not get contents from stream with mode %s',
                    stream_get_meta_data($this->resource)['mode']
                )
            );
        }

        return $contents;
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        return $key !== null
            ? stream_get_meta_data($this->resource)[$key]
            : stream_get_meta_data($this->resource);
    }

    /**
     * @throws RuntimeException
     */
    private function assertResource(): void
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Resource is detached from stream.');
        }
    }

    private function streamModeMatches(string $pattern): bool
    {
        return is_resource($this->resource)
            && preg_match(
                $pattern,
                stream_get_meta_data($this->resource)['mode']
            ) === 1;
    }
}
