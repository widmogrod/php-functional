<?php

declare(strict_types=1);

namespace test\Functional;

use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;

class FilterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
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

    public function provideData()
    {
        return [
            'simple list' => [
                '$list' => fromIterable([1, 2, 3, 4, 5]),
                '$expected' => fromIterable([1, 3, 5])
            ],
            'empty list' => [
                '$list' => fromNil(),
                '$expected' => fromNil()
            ],
            'traversable' => [
                '$list' => fromIterable(new \ArrayIterator([1, 2, 3, 4, 5])),
                '$expected' => fromIterable([1, 3, 5]),
            ],
        ];
    }
}
