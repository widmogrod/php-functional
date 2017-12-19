<?php

namespace example;

use function Widmogrod\Functional\fromValue;
use Widmogrod\Primitive\Listt;

class ListComprehensionWithMonadTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_combine_two_lists()
    {
        // [1,2] >>= \n -> ['a','b'] >>= \ch -> return (n,ch) == [(1,'a'),(1,'b'),(2,'a'),(2,'b')]
        $result = Listt::of([1, 2])
            ->bind(function ($n) {
                return Listt::of(['a', 'b'])
                    ->bind(function ($x) use ($n) {
                        return fromValue([$n, $x]);
                    });
            });

        $this->assertEquals(
            Listt::of([
                [1, 'a'],
                [1, 'b'],
                [2, 'a'],
                [2, 'b']
            ]),
            $result
        );
    }
}
