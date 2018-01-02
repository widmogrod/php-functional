<?php

declare(strict_types=1);

namespace example;

use Widmogrod\Monad\Identity;
use function Widmogrod\Monad\Control\Doo\doo;
use function Widmogrod\Monad\Control\Doo\in;
use function Widmogrod\Monad\Control\Doo\let;

class FreeDooDSLTest extends \PHPUnit\Framework\TestCase
{
    public function test_example_with_do_notation()
    {
        $result = doo(
            let('a', Identity::of(1)),
            let('b', Identity::of(3)),
            let('c', in(['a', 'b'], function (int $a, int $b): Identity {
                return Identity::of($a + $b);
            })),
            in(['c'], function (int $c): Identity {
                return Identity::of($c * $c);
            })
        );

        $this->assertEquals(Identity::of(16), $result);
    }

    public function test_example_without_do_notation()
    {
        $result = Identity::of(1)
            ->bind(function ($a) {
                return Identity::of(3)
                    ->bind(function ($b) use ($a) {
                        return Identity::of($a + $b)
                            ->bind(function ($c) {
                                return Identity::of($c * $c);
                            });
                    });
            });

        $this->assertEquals(Identity::of(16), $result);
    }
}
