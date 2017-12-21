<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\repeat;
use function Widmogrod\Functional\take;
use function Widmogrod\Functional\zip;

class ZipTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

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
                '$a' => fromNil(),
                '$b' => fromNil(),
                '$expected' => fromNil(),
            ],
            'zipping of two lists when left is an empty list' => [
                '$a' => fromNil(),
                '$b' => fromIterable([1, 2, 3, 4]),
                '$expected' => fromNil(),
            ],
            'zipping of two lists when right is an empty list' => [
                '$a' => fromIterable([1, 2, 3, 4]),
                '$b' => fromNil(),
                '$expected' => fromNil(),
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

    public function test_it_should_work_on_infinite_lists()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\string(),
            Generator\string()
        )->then(function ($n, $a, $b) {
            $list = take($n, zip(repeat($a), repeat($b)));

            $this->assertEquals($n, length(filter(eql([$a, $b]), $list)));
        });
    }
}
