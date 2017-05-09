<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;

final class RawParsedBody implements ParsedBody
{
    private $parsedBody;

    public function __construct($parsedBody = null)
    {
        if (!$this->isValid($parsedBody)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parsed body needs to be either array, stdClass or null. %s provided',
                    gettype($parsedBody)
                )
            );
        }

        $this->parsedBody = $parsedBody;
    }

    /**
     * @inheritdoc
     */
    public function parse(array $contentTypes, StreamInterface $stream)
    {
        return $this->parsedBody;
    }

    private function isValid($parsedBody): bool
    {
        return is_array($parsedBody) || is_object($parsedBody) || $parsedBody === null;
    }
}
