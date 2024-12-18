<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\repeat;
use function Widmogrod\Functional\take;
use function Widmogrod\Functional\zip;

class ZipTest extends TestCase
{
    use TestTrait;

    #[DataProvider('provideData')]
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

    public static function provideData()
    {
        return [
            'zipping of two empty lists should be an empty list' => [
                fromNil(),
                fromNil(),
                fromNil(),
            ],
            'zipping of two lists when left is an empty list' => [
                fromNil(),
                fromIterable([1, 2, 3, 4]),
                fromNil(),
            ],
            'zipping of two lists when right is an empty list' => [
                fromIterable([1, 2, 3, 4]),
                fromNil(),
                fromNil(),
            ],
            'zipping of two lists when left is shorter  list' => [
                fromIterable([1, 2, 3]),
                fromIterable(['a', 'b', 'c', 'd']),
                fromIterable([
                    [1, 'a'],
                    [2, 'b'],
                    [3, 'c']
                ]),
            ],
            'zipping of two lists when right is shorter  list' => [
                fromIterable([1, 2, 3, 4]),
                fromIterable(['a', 'b', 'c']),
                fromIterable([
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
