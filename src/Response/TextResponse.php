<?php
declare(strict_types=1);

namespace Purist\Http\Response;

use Purist\Http\Header\Headers;
use Purist\Http\Message;
use Purist\Http\Stream\LazyReadOnlyTextStream;

final class TextResponse extends Response
{
    public function __construct(string $text, int $statusCode = 200, Headers $headers = null)
    {
        parent::__construct(
            new Message(new LazyReadOnlyTextStream($text), $headers),
            $statusCode
        );
    }
}
