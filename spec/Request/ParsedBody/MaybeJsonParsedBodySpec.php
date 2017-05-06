<?php

namespace spec\Purist\Http\Request\ParsedBody;

use PhpSpec\Exception\Example\FailureException;
use Psr\Http\Message\StreamInterface;
use Purist\Http\Request\ParsedBody\MaybeJsonParsedBody;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\ParsedBody\ParsedBody;
use Purist\Http\Request\ParsedBody\RawParsedBody;
use Purist\Http\Stream\LazyReadOnlyTextStream;

class MaybeJsonParsedBodySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new RawParsedBody(['test']));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MaybeJsonParsedBody::class);
        $this->shouldImplement(ParsedBody::class);
    }

    function it_should_give_parsed_json_when_content_type_is_json()
    {
        $this
            ->parse(['application/json'], new LazyReadOnlyTextStream($json = '{"test":10}'))
            ->shouldHaveProperty(['test', 10]);

        $this
            ->parse(['application/ld+json'], new LazyReadOnlyTextStream($json = '{"test":20}'))
            ->shouldHaveProperty(['test', 20]);

        $this
            ->parse(['anything/goes+json'], new LazyReadOnlyTextStream($json = '{"aTest":20}'))
            ->shouldHaveProperty(['aTest', 20]);
    }

    function it_should_give_raw_value_when_content_types_is_not_json(StreamInterface $stream)
    {
        $this
            ->parse(['text/plain'], $stream)
            ->shouldReturn(['test']);
    }

    public function getMatchers()
    {
        return [
            'haveProperty' => function ($subject, $key) {
                list($property, $value) = $key;
                if (!property_exists($subject, $property) || $subject->{$property} !== $value) {
                    throw new FailureException(sprintf(
                        'Subject "%s" did not have property "%s" with value %s.',
                        print_r($subject, true),
                        $property,
                        $value
                    ));
                }
                return true;
            }
        ];
    }
}
