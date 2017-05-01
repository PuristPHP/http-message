<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

interface ParsedBody
{
    /**
     * @param array|\stdClass|null $parsedBody
     * @return ParsedBody
     * @throws \InvalidArgumentException
     */
    public function withParsedBody($parsedBody = null): self;

    /**
     * @param string[] $contentTypes
     * @return array|\stdClass|null
     */
    public function get(array $contentTypes);
}
