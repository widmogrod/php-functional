<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Maybe
 */
class MaybeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Maybe');
        $this->shouldHaveType('Monad\MonadInterface');
        $this->shouldHaveType('Monad\LiftInterface');
    }

    public function it_should_bind_value_from_constructor_to_given_function_if_value_is_not_null()
    {
        $this->beConstructedWith(2);
        $this->bind(function ($value) {
            return $value * $value;
        })->shouldReturn(4);
    }

    public function it_should_not_bind_value_from_constructor_to_given_function_if_value_is_null()
    {
        $this->beConstructedWith(null);
        $result = $this->bind(function ($value) {
            return $value * $value;
        });

        $result->shouldReturn(null);
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
            return \Monad\Maybe::create($value + 1);
        };
        $unWrap = function ($x) {
            return $x;
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne);
        $left = $mAddOne(3);

        $right->bind($unWrap)->shouldReturn($left->bind($unWrap));
    }

    public function it_should_obey_second_monad_law()
    {
        $unWrap = function ($x) {
            return $x;
        };

        $this->beConstructedWith(3);
        $right = $this->bind(\Monad\Maybe::create);
        $left = \Monad\Unit::create(3);

        $right->bind($unWrap)->shouldReturn($left->bind($unWrap));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Maybe::create($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Maybe::create($value + 2);
        };
        $unWrap = function ($x) {
            return $x;
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne)->bind($mAddTwo);
        $left = $this->bind(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->bind($mAddTwo);
        });

        $right->bind($unWrap)->shouldReturn($left->bind($unWrap));
    }


    public function it_shoud_lift_functions()
    {
        $this->beConstructedWith(2);
        $result = $this->lift(function ($x) {
            return $x + 1;
        });
        $result->shouldHaveType('Monad\Maybe');
        $result->bind(function ($x) {
            return $x;
        })->shouldReturn(3);
    }
}
