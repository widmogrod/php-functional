<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use function Widmogrod\Functional\filterM;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Monad\Maybe\just;

class FilterMTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_filter_with_maybe(
        $list,
        $expected
    ) {
        $filter = function ($i) {
            return just($i % 2 == 1);
        };

        $this->assertEquals(
            $expected,
            filterM($filter, $list)
        );
    }

    public static function provideData()
    {
        return [
            'simple list' => [
                fromIterable([1, 2, 3, 4, 5]),
                just(fromIterable([1, 3, 5]))
            ],
            'empty list' => [
                fromNil(),
                fromNil()
            ],
            'traversable' => [
                fromIterable(new \ArrayIterator([1, 2, 3, 4, 5])),
                just(fromIterable([1, 3, 5])),
            ],
        ];
    }
}
