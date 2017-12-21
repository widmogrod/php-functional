<?php

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\zip;

class ZipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_zipped_list(
        Listt $a,
        Listt $b,
        Listt $expected
    ) {
        $result = zip($a, $b);

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
            'zipping of two empty lists should be an empty list' => [
                '$a' => fromIterable([]),
                '$b' => fromIterable([]),
                '$expected' => fromIterable([]),
            ],
            'zipping of two lists when left is an empty list' => [
                '$a' => fromIterable([]),
                '$b' => fromIterable([1, 2, 3, 4]),
                '$expected' => fromIterable([]),
            ],
            'zipping of two lists when right is an empty list' => [
                '$a' => fromIterable([1, 2, 3, 4]),
                '$b' => fromIterable([]),
                '$expected' => fromIterable([]),
            ],
            'zipping of two lists when left is shorter  list' => [
                '$a' => fromIterable([1, 2, 3]),
                '$b' => fromIterable(['a', 'b', 'c', 'd']),
                '$expected' => fromIterable([
                    [1, 'a'],
                    [2, 'b'],
                    [3, 'c']
                ]),
            ],
            'zipping of two lists when right is shorter  list' => [
                '$a' => fromIterable([1, 2, 3, 4]),
                '$b' => fromIterable(['a', 'b', 'c']),
                '$expected' => fromIterable([
                    [1, 'a'],
                    [2, 'b'],
                    [3, 'c']
                ]),
            ]
        ];
    }
}
