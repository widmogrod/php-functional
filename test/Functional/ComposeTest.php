<?php

declare(strict_types=1);

namespace test\Functional;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class ComposeTest extends TestCase
{
    public function test_it_should_retun_function_accepting_arguments()
    {
        $this->assertInstanceOf(Closure::class, f\compose('strtolower', 'strtoupper'));
    }

    #[DataProvider('provideData')]
    public function test_it_should_be_curried(
        $functions,
        $value,
        $expected
    )
    {
        $fn = f\compose(...$functions);
        $this->assertEquals(
            $expected,
            $fn($value)
        );
    }

    public static function provideData()
    {
        return [
            'two function' => [
                ['strtolower', 'strtoupper'],
                'aBcD',
                'abcd'
            ],
        ];
    }
}
