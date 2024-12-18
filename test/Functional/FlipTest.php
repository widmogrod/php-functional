<?php

declare(strict_types=1);

namespace test\Functional;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class FlipTest extends TestCase
{
    #[DataProvider('provideFunctions')]
    public function test_it_should_flip_func_arguments(
        callable $func,
        array $args,
        $expected
    ) {
        $flipped = f\flip($func);

        $this->assertEquals(
            $expected,
            $flipped(...$args)
        );
    }

    public static function provideFunctions()
    {
        return [
            'two arguments' => [
                function ($a, $b) {
                    return [$a, $b];
                },
                [1, 2],
                [2, 1]
            ],
            'three arguments' => [
                function ($a, $b, $c) {
                    return [$a, $b, $c];
                },
                [1, 2, 3],
                [2, 1, 3]
            ],
        ];
    }

    #[DataProvider('provideFunctionsWithNotEnoughArgs')]
    public function test_it_should_curry_if_not_enough_args_passed(
        callable $func,
        array $args
    ) {
        $curried = f\curry($func);

        $this->assertInstanceOf(
            Closure::class,
            $curried(...$args)
        );
    }

    public static function provideFunctionsWithNotEnoughArgs()
    {
        return [
            'two arguments' => [
                function ($a, $b) {
                    return [$a, $b];
                },
                [],
            ],
            'three arguments' => [
                function ($a, $b, $c) {
                    return [$a, $b, $c];
                },
                [1],
            ],
        ];
    }
}
