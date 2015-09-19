<?php
namespace spec\Monad\Maybe;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @mixin \Monad\Maybe\Just
 */
class JustSpec extends ObjectBehavior
{
    public function it_should_not_map_if_null()
    {
        $this->beConstructedWith(null);
        $result = $this->map(function() {
            return 666;
        });

        $result->extract()->shouldReturn(null);
    }

    public function it_should_not_apply_if_null()
    {
        $f = function($x) { return $x / 2; };

        $this->beConstructedWith(null);
        $result = $this->ap($this::of($f));

        $result->extract()->shouldReturn(null);
    }
}
