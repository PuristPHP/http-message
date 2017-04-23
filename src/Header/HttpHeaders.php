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
     * Retrieves all message header values.
     *
     * The keys represent the header name as it will be sent over the wire, and
     * each value is an array of strings associated with the header.
     *
     *     // Represent the headers as a string
     *     foreach ($message->toArray() as $name => $values) {
     *         echo $name . ": " . implode(", ", $values);
     *     }
     *
     *     // Emit headers iteratively:
     *     foreach ($message->toArray() as $name => $values) {
     *         foreach ($values as $value) {
     *             header(sprintf('%s: %s', $name, $value), false);
     *         }
     *     }
     *
     * While header names are not case-sensitive, toArray() will preserve the
     * exact case in which headers were originally specified.
     *
     * @return array Returns an associative array of the message's headers. Each
     *     key MUST be a header name, and each value MUST be an array of strings
     *     for that header.
     */
    public function toArray(): array
    {
        return array_map(function ($item) {
            return (array) $item;
        }, $this->headers);
    }

    /**
     * Checks if a header exists by the given case-insensitive name.
     *
     * @param string $name Case-insensitive header field name.
     * @return bool Returns true if any header names match the given header
     *     name using a case-insensitive string comparison. Returns false if
     *     no matching header name is found in the message.
     */
    public function has($name): bool
    {
        return array_key_exists(
            mb_strtolower($name),
            array_change_key_case($this->headers, CASE_LOWER)
        );
    }

    /**
     * Retrieves a message header value by the given case-insensitive name.
     *
     * This method returns an array of all the header values of the given
     * case-insensitive header name.
     *
     * If the header does not appear in the message, this method MUST return an
     * empty array.
     *
     * @param string $name Case-insensitive header field name.
     * @return string[] An array of string values as provided for the given
     *    header. If the header does not appear in the message, this method MUST
     *    return an empty array.
     */
    public function header($name): array
    {
        if (!$this->has($name)) {
            return [];
        }

        return (array) array_change_key_case($this->headers, CASE_LOWER)[mb_strtolower($name)];
    }

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use header() instead
     * and supply your own delimiter when concatenating.
     *
     * If the header does not appear in the message, this method MUST return
     * an empty string.
     *
     * @param string $name Case-insensitive header field name.
     * @return string A string of values as provided for the given header
     *    concatenated together using a comma. If the header does not appear in
     *    the message, this method MUST return an empty string.
     */
    public function headerLine($name): string
    {
        return implode(',', $this->header($name));
    }

    /**
     * Return an instance with the provided value replacing the specified header.
     *
     * While header names are case-insensitive, the casing of the header will
     * be preserved by this function, and returned from toArray().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new and/or updated header addvalue.
     *
     * @param string $name Case-insensitive header field name.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
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
     * Return an instance with the specified header appended with the given value.
     *
     * Existing values for the specified header will be maintained. The new
     * value(s) will be appended to the existing list. If the header did not
     * exist previously, it will be added.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * new header and/or value.
     *
     * @param string $name Case-insensitive header field name to add.
     * @param string|string[] $value Header value(s).
     * @return self
     * @throws \InvalidArgumentException for invalid header names or values.
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
     * Return an instance without the specified header.
     *
     * Header resolution MUST be done without case-sensitivity.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the named header.
     *
     * @param string $name Case-insensitive header field name to remove.
     * @return self
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
