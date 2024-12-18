<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_filter_with_maybe(
        $list,
        $expected
    ) {
        $filter = function (int $i): bool {
            return $i % 2 === 1;
        };

        $this->assertEquals(
            $expected,
            filter($filter, $list)
        );
    }

    public static function provideData()
    {
        return [
            'simple list' => [
                fromIterable([1, 2, 3, 4, 5]),
                fromIterable([1, 3, 5])
            ],
            'empty list' => [
                fromNil(),
                fromNil()
            ],
            'traversable' => [
                fromIterable(new \ArrayIterator([1, 2, 3, 4, 5])),
                fromIterable([1, 3, 5]),
            ],
        ];
    }
}
