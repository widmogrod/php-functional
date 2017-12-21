<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\repeat;
use function Widmogrod\Functional\take;

class TakeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it(
        Listt $a,
        int $n,
        Listt $expected
    ) {
        $result = take($n, $a);

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
                '$n' => 1,
                '$expected' => fromNil(),
            ],
            'should empty list when n is zero' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$n' => 0,
                '$expected' => fromNil(),
            ],
            'should empty list when n is negative' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$n' => random_int(-1000, -1),
                '$expected' => fromNil(),
            ],
            'should return part of finite list' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$n' => 3,
                '$expected' => fromIterable([1, 2, 3]),
            ],
            'should return whole list when take more than in the list' => [
                '$a' => fromIterable([1, 2, 3, 4, 5]),
                '$n' => 3000,
                '$expected' => fromIterable([1, 2, 3, 4, 5]),
            ],
            'should return part of infinite list' => [
                '$a' => repeat('a'),
                '$n' => 3,
                '$expected' => fromIterable(['a', 'a', 'a']),
            ],
        ];
    }
}
