<?php
namespace spec\Monad\Either;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Either\Right
 */
class RightSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Either\Right');
        $this->shouldHaveType('FantasyLand\MonadInterface');
        $this->shouldHaveType('Monad\Either\Either');
    }

    public function it_should_obey_first_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Either\Right::of($value + 1);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne);
        $left = $mAddOne(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(\Monad\Either\Right::of);
        $left = \Monad\Either\Right::of(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Either\Right::of($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Either\Right::of($value + 2);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne)->bind($mAddTwo);
        $left = $this->bind(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->bind($mAddTwo);
        });

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }
}
