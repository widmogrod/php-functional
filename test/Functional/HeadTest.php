<?php

namespace test\Functional;

use Widmogrod\Functional as f;

class HeadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_take_head_of_a_list(
        $list,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            f\head($list)
        );
    }

    public function provideData()
    {
        return [
            'simple list' => [
                '$list'     => [1, 2],
                '$expected' => 1
            ],
            'empty list' => [
                '$list'     => [],
                '$expected' => null
            ],
            'array iterator' => [
                '$list'     => new \ArrayIterator([1, 2]),
                '$expected' => 1
            ],
            'array object' => [
                '$list'     => new \ArrayObject([1, 2]),
                '$expected' => 1
            ],
        ];
    }
}
