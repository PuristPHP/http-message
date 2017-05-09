<?php

namespace spec\Purist\Http\Request\ParsedBody;

use PhpSpec\Exception\Example\FailureException;
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
            ->parse(['text/xml'], new LazyReadOnlyTextStream('<XmlTag>Hello</XmlTag>'))
            ->callOnWrappedObject('xpath', ['/XmlTag'])
            ->shouldHaveSimpleXMLElementWithText('Hello');

        $this
            ->parse(['application/rss+xml'], new LazyReadOnlyTextStream('<RssTag>World</RssTag>'))
            ->callOnWrappedObject('xpath', ['/RssTag'])
            ->shouldHaveSimpleXMLElementWithText('World');

        $this
            ->parse(['anyThing/Goes+XML'], new LazyReadOnlyTextStream('<How>youdoin?</How>'))
            ->callOnWrappedObject('xpath', ['/How'])
            ->shouldHaveSimpleXMLElementWithText('youdoin?');
    }

    function it_should_give_raw_value_when_content_types_is_not_xml(StreamInterface $stream)
    {
        $this
            ->parse(['text/plain'], $stream)
            ->shouldReturn(['test']);
    }

    public function getMatchers()
    {
        return [
            'haveSimpleXMLElementWithText' => function ($subject, $key) {
                if (!$subject[0] instanceof \SimpleXMLElement
                    || (string) $subject[0] !== $key
                ) {
                    throw new FailureException(sprintf(
                        'Return value needs to be an array where first element is SimpleXMLElement and the string value needs to be %s',
                        (string) $subject[0]
                    ));
                }
                return true;
            }
        ];
    }
}
