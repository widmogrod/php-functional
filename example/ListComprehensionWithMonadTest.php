<?php

declare(strict_types=1);

namespace example;

use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromValue;

class ListComprehensionWithMonadTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_combine_two_lists()
    {
        // [1,2] >>= \n -> ['a','b'] >>= \ch -> return (n,ch) == [(1,'a'),(1,'b'),(2,'a'),(2,'b')]
        $result = fromIterable([1, 2])
            ->bind(function ($n) {
                return fromIterable(['a', 'b'])
                    ->bind(function ($x) use ($n) {
                        return fromValue([$n, $x]);
                    });
            });

        $this->assertEquals(
            fromIterable([
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b']
            ]),
            $result
        );
    }
}
