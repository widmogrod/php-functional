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
    }

    public function it_should_bind_value_from_constructor_to_given_function()
    {
        $this->beConstructedWith(2);
        $this->bind(function($value) {
            return $value * $value;
        })->shouldReturn(4);
    }
}
