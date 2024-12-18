<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class PipeTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_compose_and_inject_input_correctly(
        $functions,
        $value,
        $expected
    )
    {
        $this->assertEquals(
            $expected,
            f\pipe($value, ...$functions)
        );
    }

    public static function provideData()
    {
        return [
            'two function' => [
                ['strtolower', 'strtoupper'],
                'aBcD',
                'ABCD'
            ],
        ];
    }
}
