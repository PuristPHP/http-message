<?php

namespace spec\Purist\Http\Request;

use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Purist\Http\Request\Uri;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class UriSpec extends ObjectBehavior
{
    const TEST_URL = 'https://nicholas:ruunu@test-url.com:42/with/path?query=something&another#fragment';

    function let()
    {
        $this->beConstructedWith(self::TEST_URL);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Uri::class);
        $this->shouldImplement(UriInterface::class);
    }

    public function it_returns_scheme()
    {
        $this->getScheme()->shouldReturn('https');
    }

    public function it_returns_authority()
    {
        $this->getAuthority()->shouldReturn('nicholas:ruunu@test-url.com:42');
    }

    public function it_returns_user_info()
    {
        $this->getUserInfo()->shouldReturn('nicholas:ruunu');
    }

    public function it_returns_host()
    {
        $this->getHost()->shouldReturn('test-url.com');
    }

    public function it_returns_port()
    {
        $this->getPort()->shouldReturn(42);
    }

    public function it_returns_path()
    {
        $this->getPath()->shouldReturn('/with/path');
    }

    public function it_returns_a_new_instance_with_changed_scheme()
    {
        $this->withScheme('HTTP')->callOnWrappedObject('getScheme')->shouldReturn('http');
    }

    public function it_returns_a_new_instance_with_changed_user_info()
    {
        $this
            ->withUserInfo('zlatan', 'ibrahimovic')
            ->callOnWrappedObject('getUserInfo')
            ->shouldReturn('zlatan:ibrahimovic');
    }

    public function it_returns_a_new_instance_with_changed_host()
    {
        $this
            ->withHost('another-url.com')
            ->callOnWrappedObject('getHost')
            ->shouldReturn('another-url.com');
    }

    public function it_returns_a_new_instance_with_changed_port()
    {
        $this
            ->withPort('45')
            ->callOnWrappedObject('getPort')
            ->shouldReturn(45);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('withPort', [65536]);

        $this->shouldThrow(InvalidArgumentException::class)
            ->during('withPort', [0]);
    }

    public function it_returns_a_new_instance_with_changed_path()
    {
        $this
            ->withPath('/another path')
            ->callOnWrappedObject('getPath')
            ->shouldReturn('/another%20path');

        $this
            ->withPath('/another%20path')
            ->callOnWrappedObject('getPath')
            ->shouldReturn('/another%20path');
    }

    public function it_returns_a_new_instance_with_changed_query()
    {
        $this
            ->withQuery('?something=hello %26&bye')
            ->callOnWrappedObject('getQuery')
            ->shouldReturn('something=hello%20%26&bye');
    }

    public function it_returns_a_new_instance_with_changed_fragment()
    {
        $this
            ->withFragment('#something')
            ->callOnWrappedObject('getFragment')
            ->shouldReturn('something');
    }

    public function it_casts_to_string()
    {
        $this->__toString()->shouldReturn(self::TEST_URL);
    }
}
