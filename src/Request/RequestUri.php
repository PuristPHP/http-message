<?php
declare(strict_types=1);

namespace Purist\Http\Request;

use Psr\Http\Message\RequestInterface;

final class RequestUri
{
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    public function toString(): string
    {
        return $this->request->getUri()->getPath();
    }

    public function match($uri): bool
    {
        return $this->toString() === $uri;
    }
}
