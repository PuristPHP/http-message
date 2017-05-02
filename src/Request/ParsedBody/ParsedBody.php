<?php
declare(strict_types=1);

namespace Purist\Http\Request\ParsedBody;

interface ParsedBody
{
    /**
     * @param string[] $contentTypes
     * @return array|\stdClass|null
     */
    public function get(array $contentTypes);
}
