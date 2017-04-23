<?php

namespace Purist\Http\Stream;

use Psr\Http\Message\StreamInterface;

class MemoizedStream
{
    private $streamConstructor;
    private $stream;

    public function __construct(callable $streamConstructor)
    {
        $this->streamConstructor = $streamConstructor;
    }

    public function get(): StreamInterface
    {
        if ($this->stream === null) {
            $this->stream = ($this->streamConstructor)();
        }

        return $this->stream;
    }
}
