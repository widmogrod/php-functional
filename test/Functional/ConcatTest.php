<?php
namespace test\Functional;

use Functional as f;

class ConcatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_values_to_array(
        $array,
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\concat($array, $value));
    }

    public function provideData()
    {
        return [
            'integer' => [
                '$array' => ['foo'],
                '$value' => 1,
                '$expected' => ['foo', 1],
            ],
            'string' => [
                '$array' => ['foo'],
                '$value' => 'bar',
                '$expected' => ['foo', 'bar'],
            ],
            'list' => [
                '$array' => ['foo'],
                '$value' => ['bar', 'baz'],
                '$expected' => ['foo', 'bar', 'baz'],
            ],
            'map' => [
                '$array' => ['foo'],
                '$value' => ['x' => 'bar', 'y' => 'baz'],
                '$expected' => ['foo', 'bar', 'baz'],
            ],
            'empty array' => [
                '$array' => ['foo'],
                '$value' => [],
                '$expected' => ['foo'],
            ],
            'null' => [
                '$array' => ['foo'],
                '$value' => null,
                '$expected' => ['foo', null],
            ],
            'list with null' => [
                '$array' => ['foo'],
                '$value' => [null],
                '$expected' => ['foo', null],
            ],
        ];
    }
}
