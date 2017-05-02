<?php
declare(strict_types=1);

namespace spec\Purist\Http\Request\ParsedBody;

use Purist\Http\Request\ParsedBody\JsonParsedBody;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\ParsedBody\ParsedBody;
use Purist\Http\Request\ParsedBody\RawParsedBody;

class JsonParsedBodySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new RawParsedBody(['test']));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(JsonParsedBody::class);
        $this->shouldImplement(ParsedBody::class);
    }

    function it_should_give_parsed_json_when_content_type_is_json()
    {
        $this->get(['application/json'])->shouldReturn(json_encode(['test']));
    }
}
