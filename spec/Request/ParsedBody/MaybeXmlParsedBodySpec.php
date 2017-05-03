<?php

namespace spec\Purist\Http\Request\ParsedBody;

use Psr\Http\Message\StreamInterface;
use Purist\Http\Request\ParsedBody\ParsedBody;
use Purist\Http\Request\ParsedBody\MaybeXmlParsedBody;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Purist\Http\Request\ParsedBody\RawParsedBody;
use Purist\Http\Stream\LazyReadOnlyTextStream;

class MaybeXmlParsedBodySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new RawParsedBody(['test']));
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MaybeXmlParsedBody::class);
        $this->shouldImplement(ParsedBody::class);
    }

    function it_should_give_parsed_xml_when_content_type_is_xml()
    {
        $this
            ->parse(['text/xml'], new LazyReadOnlyTextStream($xml = '<XmlTag>Hello</XmlTag>'))
            ->shouldReturn(iterator_to_array(simplexml_load_string($xml)));
    }

    function it_should_give_raw_value_when_content_types_is_not_xml(StreamInterface $stream)
    {
        $this
            ->parse(['text/plain'], $stream)
            ->shouldReturn(['test']);
    }
}
