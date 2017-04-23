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
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        rewind($this->resource);
        return (string) stream_get_contents($this->resource);
    }

    /**
     * Closes the stream and any underlying resources.
     *
     */
    public function close()
    {
        fclose($this->resource);
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $resource = $this->resource;
        $this->resource = null;
        return $resource;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if (!is_resource($this->resource)) {
            return null;
        }

        return fstat($this->resource)['size'] ?? null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws RuntimeException on error.
     */
    public function tell()
    {
        $this->assertResource();

        if (false === $position = ftell($this->resource)) {
            throw new RuntimeException('Could not get position of stream');
        }

        return $position;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return $this->resource === null || feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return is_resource($this->resource) && (
            stream_get_meta_data($this->resource)['seekable'] ?? false
        );
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->assertResource();

        if (fseek($this->resource, $offset, $whence) === -1) {
            throw new RuntimeException('Failed to seek stream.');
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @see seek()
     * @link http://www.php.net/manual/en/function.fseek.php
     * @throws RuntimeException on failure.
     */
    public function rewind()
    {
        $this->assertResource();

        if (!$this->isSeekable()) {
            throw new RuntimeException('Could not rewind not seekable stream.');
        }

        rewind($this->resource);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return is_resource($this->resource) && preg_match(
            '(r\+|w|a|x|c)',
            stream_get_meta_data($this->resource)['mode']
        ) === 1;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws RuntimeException on failure.
     */
    public function write($string)
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
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return is_resource($this->resource) && preg_match(
            '(r\+?|(w|c|a)\+)',
            stream_get_meta_data($this->resource)['mode']
        ) === 1;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws RuntimeException if an error occurs.
     */
    public function read($length)
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
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
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
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        return $key !== null
            ? stream_get_meta_data($this->resource)[$key]
            : stream_get_meta_data($this->resource);
    }

    /**
     * @throws RuntimeException if resource is detached from stream
     */
    private function assertResource()
    {
        if (!is_resource($this->resource)) {
            throw new RuntimeException('Resource is detached from stream.');
        }
    }
}
