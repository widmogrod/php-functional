<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Functional as f;

class IdentityTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_return_given_value(
        $value
    ) {
        $this->assertEquals($value, f\identity($value));
    }

    public static function provideData()
    {
        return [
            'integer' => [
                1,
            ],
            'string' => [
                'bar',
            ],
            'list' => [
                ['bar', 'baz'],
            ],
            'map' => [
                ['x' => 'bar', 'y' => 'baz'],
            ],
        ];
    }
}
