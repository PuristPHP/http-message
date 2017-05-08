<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

use Psr\Http\Message\StreamInterface;

final class MaybeXmlParsedBody implements ParsedBody
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
        $contentTypes = array_map('mb_strtolower', $contentTypes);

        if (
            !in_array('text/xml', $contentTypes, true)
            && !in_array('application/xml', $contentTypes, true)
        ) {
            return $this->parsedBody->parse($contentTypes, $stream);
        }

        return simplexml_load_string($stream->getContents());
    }
}
