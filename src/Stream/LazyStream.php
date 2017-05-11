<?php
declare(strict_types=1);

namespace Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;

final class LazyStream implements StreamInterface
{
    private $memoizedStream;

    public function __construct($fileName, $mode)
    {
        $this->memoizedStream = new MemoizedStream(function () use ($fileName, $mode) {
            return new Stream(fopen($fileName, $mode));
        });
    }

    /**
     * @inheritdoc
     */
    public function __toString(): string
    {
        return (string) $this->memoizedStream->get();
    }

    /**
     * @inheritdoc
     */
    public function close(): void
    {
        $this->memoizedStream->get()->close();
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        return $this->memoizedStream->get()->detach();
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return $this->memoizedStream->get()->getSize();
    }

    /**
     * @inheritdoc
     */
    public function tell(): int
    {
        return $this->memoizedStream->get()->tell();
    }

    /**
     * @inheritdoc
     */
    public function eof(): bool
    {
        return $this->memoizedStream->get()->eof();
    }

    /**
     * @inheritdoc
     */
    public function isSeekable(): bool
    {
        return $this->memoizedStream->get()->isSeekable();
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        $this->memoizedStream->get()->seek($offset, $whence);
    }

    /**
     * @inheritdoc
     */
    public function rewind(): void
    {
        $this->memoizedStream->get()->rewind();
    }

    /**
     * @inheritdoc
     */
    public function isWritable(): bool
    {
        return $this->memoizedStream->get()->isWritable();
    }

    /**
     * @inheritdoc
     */
    public function write($string): int
    {
        return $this->memoizedStream->get()->write($string);
    }

    /**
     * @inheritdoc
     */
    public function isReadable(): bool
    {
        return $this->memoizedStream->get()->isReadable();
    }

    /**
     * @inheritdoc
     */
    public function read($length): string
    {
        return $this->memoizedStream->get()->read($length);
    }

    /**
     * @inheritdoc
     */
    public function getContents(): string
    {
        return $this->memoizedStream->get()->getContents();
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        return $this->memoizedStream->get()->getMetadata($key);
    }
}
