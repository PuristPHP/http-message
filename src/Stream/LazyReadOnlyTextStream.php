<?php
declare(strict_types=1);

namespace Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;

final class LazyReadOnlyTextStream implements StreamInterface
{
    private $stream;

    public function __construct(string $text = '')
    {
        $this->stream = new LazyStream(sprintf('data://text/plain,%s', $text), 'r');
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return (string) $this->stream;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        $this->stream->close();
    }

    /**
     * @inheritdoc
     */
    public function detach()
    {
        return $this->stream->detach();
    }

    /**
     * @inheritdoc
     */
    public function getSize()
    {
        return $this->stream->getSize();
    }

    /**
     * @inheritdoc
     */
    public function tell()
    {
        return $this->stream->tell();
    }

    /**
     * @inheritdoc
     */
    public function eof()
    {
        return $this->stream->eof();
    }

    /**
     * @inheritdoc
     */
    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }

    /**
     * @inheritdoc
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        return $this->stream->seek($offset, $whence);
    }

    /**
     * @inheritdoc
     */
    public function rewind()
    {
        return $this->stream->rewind();
    }

    /**
     * @inheritdoc
     */
    public function isWritable()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function write($string)
    {
        return $this->stream->write($string);
    }

    /**
     * @inheritdoc
     */
    public function isReadable()
    {
        return $this->stream->isReadable();
    }

    /**
     * @inheritdoc
     */
    public function read($length)
    {
        return $this->stream->read($length);
    }

    /**
     * @inheritdoc
     */
    public function getContents()
    {
        return $this->stream->getContents();
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }
}
