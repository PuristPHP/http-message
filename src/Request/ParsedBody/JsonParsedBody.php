<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

final class JsonParsedBody implements ParsedBody
{
    private $parsedBody;

    public function __construct(ParsedBody $parsedBody)
    {
        $this->parsedBody = $parsedBody;
    }

    /**
     * @inheritdoc
     */
    public function get(array $contentTypes)
    {
        $parsedBody = $this->parsedBody->get($contentTypes);

        return array_reduce(
            $contentTypes,
            function ($carry, string $contentType) use ($parsedBody) {
                if ($contentType === 'application/json') {
                    $carry = json_encode($parsedBody);
                }
                return $carry;
            }
        ) ?? $parsedBody;
    }
}
