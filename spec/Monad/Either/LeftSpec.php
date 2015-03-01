<?php
namespace spec\Monad\Either;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Either\Left
 */
class LeftSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Either\Left');
        $this->shouldHaveType('Monad\MonadInterface');
        $this->shouldHaveType('Monad\Either\EitherInterface');
    }

    public function it_should_not_bind()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(function($e) {
            throw new \Exception('This should never been thrown');
        });
        $right->shouldReturn(null);
    }

    public function it_should_obey_first_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Unit::create($value + 1);
        };

        $this->beConstructedWith(3);
        $right = $this->orElse($mAddOne);
        $left = $mAddOne(3);

        $right->bind(\Monad\Utils::returns)->shouldReturn($left->bind(\Monad\Utils::returns));
    }

    public function it_should_obey_second_monad_law()
    {
        $this->beConstructedWith(3);
        $right = $this->orElse(\Monad\Either\Left::create);
        $left = \Monad\Either\Left::create(3);

        $right->bind(\Monad\Utils::returns)->shouldReturn($left->bind(\Monad\Utils::returns));
    }

    public function it_should_obey_third_monad_law()
    {
        $mAddOne = function ($value) {
            return \Monad\Either\Left::create($value + 1);
        };
        $mAddTwo = function ($value) {
            return \Monad\Either\Left::create($value + 2);
        };

        $this->beConstructedWith(3);
        $right = $this->orElse($mAddOne)->orElse($mAddTwo);
        $left = $this->orElse(function ($x) use ($mAddOne, $mAddTwo) {
            return $mAddOne($x)->orElse($mAddTwo);
        });

        $right->bind(\Monad\Utils::returns)->shouldReturn($left->bind(\Monad\Utils::returns));
    }
}
