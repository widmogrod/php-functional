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
        $this->shouldHaveType('FantasyLand\MonadInterface');
        $this->shouldHaveType('Monad\Either\Either');
    }

    public function it_should_not_bind()
    {
        $this->beConstructedWith(3);
        $right = $this->bind(function ($e) {
            throw new \Exception('This should never been thrown');
        });
        $right->shouldReturn($right);
    }
}
