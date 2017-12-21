<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class PushTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_append_array_with_array_values(
        $array,
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\push_($array, $value));
    }

    public function provideData()
    {
        return [
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
            'list with null' => [
                '$array' => ['foo'],
                '$value' => [null],
                '$expected' => ['foo', null],
            ],
        ];
    }
}
