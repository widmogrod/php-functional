<?php

namespace spec\Monad;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MaybeSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->beConstructedWith(null);
        $this->shouldHaveType('Monad\Maybe');
        $this->shouldHaveType('Monad\MonadInterface');
    }

    public function it_should_bind_value_from_constructor_to_given_function_if_value_is_not_null()
    {
        $this->beConstructedWith(2);
        $this->bind(function($value) {
            return $value * $value;
        })->shouldReturn(4);
    }

    public function it_should_not_bind_value_from_constructor_to_given_function_if_value_is_null()
    {
        $this->beConstructedWith(null);
        $result = $this->bind(function($value) {
            return $value * $value;
        });

        $result->shouldBeAnInstanceOf('Monad\Unit');
        $result->bind(function($value) {
            return $value;
        })->shouldReturn(null);
    }
}
