<?php
declare(strict_types=1);

namespace Purist\Http\Header;

final class HttpHeaders implements Headers
{
    private $headers;

    public function __construct(array $headers = [])
    {
        $this->headers = $headers;
    }

    /**
     * @inheritdoc
     */
    public function toArray(): array
    {
        return array_map(function ($item) {
            return (array) $item;
        }, $this->headers);
    }

    /**
     * @inheritdoc
     */
    public function has($name): bool
    {
        return array_key_exists(
            mb_strtolower($name),
            array_change_key_case($this->headers, CASE_LOWER)
        );
    }

    /**
     * @inheritdoc
     */
    public function header($name): array
    {
        if (!$this->has($name)) {
            return [];
        }

        return (array) array_change_key_case($this->headers, CASE_LOWER)[mb_strtolower($name)];
    }

    /**
     * @inheritdoc
     */
    public function headerLine($name): string
    {
        return implode(',', $this->header($name));
    }

    /**
     * @inheritdoc
     */
    public function replace($name, $value): Headers
    {
        $header = new ValidHeader($name, $value);

        return new self(
            array_merge(
                $this->remove($header->name())->toArray(),
                [
                    $header->name() => (array) $header->value(),
                ]
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function add($name, $value): Headers
    {
        $header = new ValidHeader($name, $value);

        if (!$this->has($header->name())) {
            return $this->replace($header->name(), $header->value());
        }

        return new self(
            array_merge(
                $this->headers,
                array_map(
                    function ($item) use ($header) {
                        return array_merge($item, (array) $header->value());
                    },
                    array_filter(
                        $this->headers,
                        function ($key) use ($header) {
                            return mb_strtolower($key) === mb_strtolower($header->name());
                        },
                        ARRAY_FILTER_USE_KEY
                    )
                )
            )
        );
    }

    /**
     * @inheritdoc
     */
    public function remove($name): Headers
    {
        return new self(
            array_filter(
                $this->headers,
                function ($header) use ($name) {
                    return mb_strtolower($name) !== mb_strtolower($header);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }
}
