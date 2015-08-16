<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Identity
 */
class IdentitySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Identity');
        $this->shouldHaveType('FantasyLand\MonadInterface');
    }

    public function it_should_bind_value_from_constructor_to_given_function()
    {
        $this->beConstructedWith(2);
        $this->bind(function ($value) {
            return $value * $value;
        })->shouldReturn(4);
    }

    public function it_should_obey_first_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Identity::of($value + 1);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne);
        $left = $mAddOne(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(\Monad\Identity::of);
        $left = \Monad\Identity::of(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Identity::of($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Identity::of($value + 2);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne)->bind($mAddTwo);
        $left = $this->bind(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->bind($mAddTwo);
        });

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_identity_law_applicative()
    {
        $this->beConstructedWith(function ($x) {
            return $x;
        });
        $result = $this->ap($this::of(1));

        $result->extract()->shouldReturn(1);
    }

    public function it_should_obey_homomorphism_law_applicative()
    {
        $id = function ($x) {
            return $x;
        };
        $this->beConstructedWith($id);
        $result = $this->ap($this::of(1));

        $result->extract()->shouldReturn(
            $this::of($id(1))->extract()
        );
    }

    public function it_should_obey_interchange_law_applicative()
    {
        $y = 1;
        $f = function ($x) {
            return $x / 2;
        };

        $this->beConstructedWith($f);
        $result = $this->ap($this::of($y));

        $result->extract()->shouldReturn(
            $this::of(function ($f) use ($y) {
                return $f($y);
            })->ap($this)->extract()
        );
    }

    public function it_should_obey_identity_law_functor()
    {
        $this->beConstructedWith(1);
        $result = $this->map(function ($x) {
            return $x;
        });

        $result->extract()->shouldReturn(1);
    }

    public function it_should_obey_composition_law_functor()
    {
        $a = function ($x) {
            return $x + 1;
        };
        $b = function ($x) {
            return $x + 2;
        };
        $this->beConstructedWith(1);

        $result = $this->map($a)->map($b);
        $result->extract()->shouldReturn($b($a(1)));
    }
}
