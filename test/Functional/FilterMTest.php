<?php

namespace test\Functional;

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe\Just;

class FilterMTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_filter_with_maybe(
        $list,
        $expected
    ) {
        $filter = function ($i) {
            return new Just($i % 2 == 1);
        };

        $this->assertEquals(
            $expected,
            f\filterM($filter, $list)->extract()
        );
    }

    public function provideData()
    {
        return [
            'simple list' => [
                '$list'     => [1, 2, 3, 4, 5],
                '$expected' => [1, 3, 5]
            ],
            'empty list' => [
                '$list'     => [],
                '$expected' => []
            ],
            'traversable' => [
                '$list'     => new \ArrayIterator([1, 2, 3, 4, 5]),
                '$expected' => [1, 3, 5]
            ],
        ];
    }
}
