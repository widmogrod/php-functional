<?php

namespace test\Functional;

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe as m;

class FoldMTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_work_with_maybe(
        $list,
        $expected
    ) {
        $addSingleDigit = function ($acc, $i) {
            return $i > 9 ? m\nothing() : m\just($acc + $i);
        };
        $this->assertEquals(
            $expected,
            f\foldM($addSingleDigit, 0, $list)->extract()
        );
    }

    public function provideData()
    {
        return [
            'just' => [
                '$list'     => [1, 3, 5, 7],
                '$expected' => 16
            ],
            'nothing' => [
                '$list'     => [1, 3, 42, 7],
                '$expected' => null
            ],
            'empty array' => [
                '$list'     => [],
                '$expected' => 0
            ],
            'traversable' => [
                '$list'     => new \ArrayIterator([1, 3, 5, 7]),
                '$expected' => 16
            ],
        ];
    }
}
