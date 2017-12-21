<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class FlipTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideFunctions
     */
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

    public function provideFunctions()
    {
        return [
            'two arguments' => [
                '$func' => function ($a, $b) {
                    return [$a, $b];
                },
                '$args' => [1, 2],
                '$expected' => [2, 1]
            ],
            'three arguments' => [
                '$func' => function ($a, $b, $c) {
                    return [$a, $b, $c];
                },
                '$args' => [1, 2, 3],
                '$expected' => [2, 1, 3]
            ],
        ];
    }

    /**
     * @dataProvider provideFunctionsWithNotEnoughArgs
     */
    public function test_it_should_curry_if_not_enough_args_passed(
        callable $func,
        array $args
    ) {
        $curried = f\curry($func);

        $this->assertInstanceOf(
            \Closure::class,
            $curried(...$args)
        );
    }

    public function provideFunctionsWithNotEnoughArgs()
    {
        return [
            'two arguments' => [
                '$func' => function ($a, $b) {
                    return [$a, $b];
                },
                '$args' => [],
            ],
            'three arguments' => [
                '$func' => function ($a, $b, $c) {
                    return [$a, $b, $c];
                },
                '$args' => [1],
            ],
        ];
    }
}
