<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

use Psr\Http\Message\StreamInterface;

final class MaybeJsonParsedBody implements ParsedBody
{
    private $parsedBody;

    public function __construct(ParsedBody $parsedBody)
    {
        $this->parsedBody = $parsedBody;
    }

    /**
     * @inheritdoc
     */
    public function parse(array $contentTypes, StreamInterface $stream)
    {
        if (!in_array('application/json', array_map('mb_strtolower', $contentTypes), true)) {
            return $this->parsedBody->parse($contentTypes, $stream);
        }

        return json_decode($stream->getContents());
    }
}
