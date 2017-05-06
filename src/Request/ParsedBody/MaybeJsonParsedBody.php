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
        if (!$this->hasValidContentType($contentTypes)) {
            return $this->parsedBody->parse($contentTypes, $stream);
        }

        $json = $stream->getContents();

        if ($json && null === $decodedJson = json_decode($json)) {
            return $this->parsedBody->parse($contentTypes, $stream);
        }

        return $decodedJson;
    }

    private function hasValidContentType(array $contentTypes): bool
    {
        return (bool) array_filter(
            array_map('mb_strtolower', $contentTypes),
            function (string $contentType) {
                return $contentType === 'application/json'
                    || preg_match('(\+json$)', $contentType);
            }
        );
    }
}
