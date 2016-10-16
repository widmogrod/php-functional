<?php
namespace test\Functional;

use Widmogrod\Functional as f;

class ConcatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_values_to_array(
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\concat($value));
    }

    public function provideData()
    {
        return [
            'list' => [
                '$array' => f\toFoldable(['foo', 1]),
                '$expected' => ['foo', 1],
            ],
            'list of lists' => [
                '$array' => f\toFoldable([['a', 1], ['b', 2]]),
                '$expected' => ['a', 1, 'b', 2],
            ],
            'list of lists of lists' => [
                '$array' => f\toFoldable([
                    [
                        ['a', 1],
                        ['b', 2]
                    ],
                    [
                        ['c', 3]
                    ],
                ]),
                '$expected' => [['a', 1], ['b', 2], ['c', 3]],
            ],
            'list of lists of lists with some noregulatives' => [
                '$array' => f\toFoldable([
                    [
                        ['a', 1],
                        ['b', 2]
                    ],
                    ['c', 3]
                ]),
                '$expected' => [['a', 1], ['b', 2], 'c', 3],
            ],
        ];
    }
}
