<?php

namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Utils
 */
class UtilsSpec extends ObjectBehavior
{
    public function it_should_aggregate_monad_values()
    {
        $result = $this::aggregate([
            \Monad\Unit::create(1),
            \Monad\Unit::create(2),
            \Monad\Unit::create(3),
        ]);

        $result->shouldBeAnInstanceOf('Monad\MonadInterface');
        $result->shouldBeAnInstanceOf('Monad\ValueOfInterface');
        $result->valueOf()->shouldReturn([1, 2, 3]);
    }

    public function it_should_apply_values_from_several_monads_to_transformation_function_and_return_new_monad()
    {
        $result = $this::applyLift(\Monad\Unit::create([1, 2, 3]), function ($x, $y, $z) {
            return $x + $y + $z;
        });

        $result->shouldBeAnInstanceOf('Monad\MonadInterface');
        $result->shouldBeAnInstanceOf('Monad\ValueOfInterface');
        $result->valueOf()->shouldReturn(6);
    }

    public function it_should_apply_values_from_several_monads_to_transformation_function_and_return_result_of_transformation(
    )
    {
        $result = $this::applyBind(\Monad\Unit::create([1, 2, 3]), function ($x, $y, $z) {
            return $x + $y + $z;
        });

        $result->shouldReturn(6);
    }
}
