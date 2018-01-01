<?php

declare(strict_types=1);

namespace example;

use function Widmogrod\Monad\Control\Doo\doo;
use function Widmogrod\Monad\Control\Doo\in;
use function Widmogrod\Monad\Control\Doo\let;
use Widmogrod\Monad\Identity;

class FreeDooDSLTest extends \PHPUnit\Framework\TestCase
{
    public function test_it()
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
}
