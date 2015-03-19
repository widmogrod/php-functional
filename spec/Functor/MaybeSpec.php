<?php

namespace spec\Functor;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Functor\Maybe
 */
class MaybeSpec extends ObjectBehavior
{
    public function it_should_obey_identity_law()
    {
        $this->beConstructedWith(1);
        $result = $this->map(function ($x) {
            return $x;
        });

        $result->shouldBeAnInstanceOf('Functor\Maybe');
        $result->valueOf()->shouldReturn(1);
    }

    public function it_should_obey_composition_law()
    {
        $a = function($x) { return $x + 1; };
        $b = function($x) { return $x + 2; };
        $this->beConstructedWith(1);

        $result = $this->map($a)->map($b);
        $result->shouldBeAnInstanceOf('Functor\Maybe');
        $result->valueOf()->shouldReturn($b($a(1)));
    }

    public function it_should_not_map_if_null()
    {
        $this->beConstructedWith(null);
        $result = $this->map(function() {
            return 666;
        });

        $result->shouldBeAnInstanceOf('Functor\Maybe');
        $result->valueOf()->shouldReturn(null);
    }
}
