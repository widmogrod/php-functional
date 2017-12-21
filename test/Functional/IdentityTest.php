<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class IdentityTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_given_value(
        $value
    ) {
        $this->assertEquals($value, f\identity($value));
    }

    public function provideData()
    {
        return [
            'integer' => [
                '$value' => 1,
            ],
            'string' => [
                '$value' => 'bar',
            ],
            'list' => [
                '$value' => ['bar', 'baz'],
            ],
            'map' => [
                '$value' => ['x' => 'bar', 'y' => 'baz'],
            ],
        ];
    }
}
