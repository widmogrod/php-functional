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
        $this->shouldHaveType('Monad\MonadInterface');
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
            return \Monad\Identity::create($value + 1);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne);
        $left = $mAddOne(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(\Monad\Identity::create);
        $left = \Monad\Identity::create(3);

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Identity::create($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Identity::create($value + 2);
        };

        $this->beConstructedWith(3);
        $right = $this->bind($mAddOne)->bind($mAddTwo);
        $left = $this->bind(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->bind($mAddTwo);
        });

        $right->bind('Functional\identity')->shouldReturn($left->bind('Functional\identity'));
    }
}
