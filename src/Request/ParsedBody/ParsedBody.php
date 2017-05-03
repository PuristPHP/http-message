<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

use Psr\Http\Message\StreamInterface;

interface ParsedBody
{
    /**
     * @return array|\stdClass|null
     */
    public function parse(array $contentTypes, StreamInterface $stream);
}
