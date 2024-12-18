<?php

declare(strict_types=1);

namespace test\Functional;

use ArrayIterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Widmogrod\Functional\foldM;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

class FoldMTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_work_with_maybe(
        $list,
        $expected
    )
    {
        $addSingleDigit = function ($acc, $i) {
            return $i > 9 ? nothing() : just($acc + $i);
        };
        $this->assertEquals(
            $expected,
            foldM($addSingleDigit, 0, $list)
        );
    }

    public static function provideData()
    {
        return [
            'just' => [
                fromIterable([1, 3, 5, 7]),
                just(16)
            ],
            'nothing' => [
                fromIterable([1, 3, 42, 7]),
                nothing(),
            ],
            'empty array' => [
                fromNil(),
                fromNil(),
            ],
            'traversable' => [
                fromIterable(new ArrayIterator([1, 3, 5, 7])),
                just(16)
            ],
        ];
    }
}
