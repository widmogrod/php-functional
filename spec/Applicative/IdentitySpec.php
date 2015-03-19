<?php

namespace spec\Applicative;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;


/**
 * @mixin \Applicative\Identity
 */
class IdentitySpec extends ObjectBehavior
{
    public function it_should_obey_identity_law()
    {
        $this->beConstructedWith(function($x) { return $x; });
        $result = $this->ap($this::create(1));

        $result->shouldBeAnInstanceOf('Applicative\Identity');
        $result->valueOf()->shouldReturn(1);
    }

    public function it_should_obey_homomorphism_law()
    {
        $id = function($x) { return $x; };
        $this->beConstructedWith($id);
        $result = $this->ap($this::create(1));

        $result->shouldBeAnInstanceOf('Applicative\Identity');
        $result->valueOf()->shouldReturn(
            $this::create($id(1))->valueOf()
        );
    }

    public function it_should_obey_interchange_law()
    {
        $y = 1;
        $f = function($x) { return $x / 2; };

        $this->beConstructedWith($f);
        $result = $this->ap($this::create($y));

        $result->shouldBeAnInstanceOf('Applicative\Identity');
        $result->valueOf()->shouldReturn(
            $this::create(function($f) use ($y) {
                return $f($y);
            })->ap($this)->valueOf()
        );
    }
}
