<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class PushTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_append_array_with_array_values(
        $array,
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\push_($array, $value));
    }

    public static function provideData()
    {
        return [
            'list' => [
                ['foo'],
                ['bar', 'baz'],
                ['foo', 'bar', 'baz'],
            ],
            'map' => [
                ['foo'],
                ['x' => 'bar', 'y' => 'baz'],
                ['foo', 'bar', 'baz'],
            ],
            'empty array' => [
                ['foo'],
                [],
                ['foo'],
            ],
            'list with null' => [
                ['foo'],
                [null],
                ['foo', null],
            ],
        ];
    }
}
