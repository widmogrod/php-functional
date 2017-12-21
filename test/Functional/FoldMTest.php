<?php

declare(strict_types=1);

namespace test\Functional;

use function Widmogrod\Functional\foldM;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

class FoldMTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_work_with_maybe(
        $list,
        $expected
    ) {
        $addSingleDigit = function ($acc, $i) {
            return $i > 9 ? nothing() : just($acc + $i);
        };
        $this->assertEquals(
            $expected,
            foldM($addSingleDigit, 0, $list)
        );
    }

    public function provideData()
    {
        return [
            'just' => [
                '$list' => fromIterable([1, 3, 5, 7]),
                '$expected' => just(16)
            ],
            'nothing' => [
                '$list' => fromIterable([1, 3, 42, 7]),
                '$expected' => nothing(),
            ],
            'empty array' => [
                '$list' => fromNil(),
                '$expected' => fromNil(),
            ],
            'traversable' => [
                '$list' => fromIterable(new \ArrayIterator([1, 3, 5, 7])),
                '$expected' => just(16)
            ],
        ];
    }
}
