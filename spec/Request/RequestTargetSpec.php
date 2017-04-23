<?php

namespace spec\Purist\Http\Request;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\UriInterface;
use Purist\Http\Request\RequestTarget;
use Purist\Http\Request\Uri;

class RequestTargetSpec extends ObjectBehavior
{
    function let(UriInterface $uri)
    {
        $this->beConstructedWith($uri);
    }

    function it_returns_origin_form(UriInterface $uri)
    {
        $uri->getPath()->willReturn('/path');
        $uri->getQuery()->willReturn('query=1');
        $this->toString()->shouldReturn('/path?query=1');

        $uri->getPath()->willReturn('');
        $uri->getQuery()->willReturn('');
        $this->toString()->shouldReturn('/');

        $uri->getPath()->willReturn('');
        $uri->getQuery()->willReturn('query=1');
        $this->toString()->shouldReturn('/?query=1');
    }

    function it_returns_asterisk_form($uri)
    {
        $this->beConstructedWith($uri, RequestTarget::ASTERISK_FORM);
        $this->toString()->shouldReturn('*');
    }

    function it_returns_full_absolute_form(UriInterface $uri)
    {
        $uri->getScheme()->willReturn('https');
        $uri->getAuthority()->willReturn('user@host:1337');
        $uri->getPath()->willReturn('/path');
        $uri->getQuery()->willReturn('query=1');
        $uri->getFragment()->willReturn('fragment');
        $this->beConstructedWith($uri, RequestTarget::ABSOLUTE_FORM);
        $this->toString()->shouldReturn('https://user@host:1337/path?query=1#fragment');
    }

    function it_returns_minimum_absolute_form(UriInterface $uri)
    {
        $uri->getScheme()->willReturn('https');
        $uri->getAuthority()->willReturn('host');
        $uri->getPath()->willReturn('');
        $uri->getQuery()->willReturn('');
        $uri->getFragment()->willReturn('');
        $this->beConstructedWith($uri, RequestTarget::ABSOLUTE_FORM);
        $this->toString()->shouldReturn('https://host');
    }

    function it_returns_authority_form(UriInterface $uri)
    {
        $uri->getAuthority()->willReturn('user@host:1337');
        $this->beConstructedWith($uri, RequestTarget::AUTHORITY_FORM);
        $this->toString()->shouldReturn('user@host:1337');
    }

    function it_must_return_slash_when_no_specific_uri_and_form_is_specified()
    {
        $this->beConstructedWith(new Uri());
        $this->toString()->shouldReturn('/');
    }
}
