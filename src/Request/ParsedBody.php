<?php

declare(strict_types=1);

namespace Purist\Request;

use InvalidArgumentException;
use stdClass;

final class ParsedBody
{
    private $parsedBody;

    public function __construct($parsedBody)
    {
        if (!is_array($parsedBody) && !$parsedBody instanceof stdClass) {
            throw new InvalidArgumentException(
                sprintf(
                    'Parsed body of type %s must be either array or stdClass',
                    gettype($parsedBody)
                )
            );
        }

        $this->parsedBody = $parsedBody;
    }

    public function get()
    {
        return $this->parsedBody;
    }
}
