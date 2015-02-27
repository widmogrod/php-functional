<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Unit
 */
class UnitSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Unit');
        $this->shouldHaveType('Monad\MonadInterface');
        $this->shouldHaveType('Monad\LiftInterface');
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
            return \Monad\Unit::create($value + 1);
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
        $right = $this->bind(\Monad\Unit::create);
        $left = \Monad\Unit::create(3);

        $right->bind($unWrap)->shouldReturn($left->bind($unWrap));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Unit::create($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Unit::create($value + 2);
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
        $result->shouldHaveType('Monad\Unit');
        $result->bind(function ($x) {
            return $x;
        })->shouldReturn(3);
    }
}
