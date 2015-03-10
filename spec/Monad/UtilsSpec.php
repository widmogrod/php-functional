<?php
namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Utils
 */
class UtilsSpec extends ObjectBehavior
{
    public function it_should_reduce_to_base_monad_values()
    {
        $result = $this::reduce([
            \Monad\Unit::create(1),
            \Monad\Unit::create(2),
            \Monad\Unit::create(3),
        ], function ($base, $value) {
            return $base + $value;
        }, 0);

        $result->shouldBeAnInstanceOf('Monad\MonadInterface');
        $result->shouldBeAnInstanceOf('Common\ValueOfInterface');
        $result->valueOf()->shouldReturn(6);
    }

    public function it_should_apply_two_monads_to_function()
    {
        $result = $this::liftM2(
            \Monad\Unit::create(1),
            \Monad\Unit::create(2),
            function ($a, $b) {
                return $a + $b;
            }
        );

        $result->shouldBeAnInstanceOf('Monad\MonadInterface');
        $result->valueOf()->shouldReturn(3);
    }
}
