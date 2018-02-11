<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\dropWhile;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\lt;

class DropWhileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it(
        Listt $a,
        callable $fn,
        Listt $expected
    ) {
        $result = dropWhile($fn, $a);

        $r = print_r($result->extract(), true);
        $e = print_r($expected->extract(), true);

        $this->assertTrue(
            $result->equals($expected),
            "$e != $r"
        );
    }

    public function provideData()
    {
        return [
            'should return empty list from when input is empty list' => [
                '$a' => fromNil(),
                '$fn' => lt(3),
                '$expected' => fromNil(),
            ],
            'should provided list when < 100' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$fn' => lt(100),
                '$expected' => fromIterable([]),
            ],
            'should return part of finite list' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$fn' => lt(4),
                '$expected' => fromIterable([4, 5]),
            ],
            'should return nil list when drop more than in the list' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$fn' => lt(400),
                '$expected' => fromNil(),
            ],
        ];
    }
}
