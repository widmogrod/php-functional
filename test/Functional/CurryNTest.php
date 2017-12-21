<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class CurryNTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideArgumentsWithFunctions
     */
    public function test_it_should_return_function_event_if_function_not_accept_arguments(
        $numberOfArguments,
        $function,
        $default
    ) {
        $this->assertInternalType('callable', f\curryN($numberOfArguments, $function, $default));
    }

    public function provideArgumentsWithFunctions()
    {
        $function = function ($a, $b, $c) {
            return sprintf('should not return value (%s, %s, %s)', $a, $b, $c);
        };

        return [
            'curry N = 0' => [
                '$numberOfArguments' => 0,
                '$function' => $function,
                '$default' => [],
            ],
            'curry with default arguments for non argument curry' => [
                '$numberOfArguments' => 0,
                '$function' => $function,
                '$default' => ['test'],
            ],
            'curry one' => [
                '$numberOfArguments' => 1,
                '$function' => $function,
                '$default' => [],
            ],
            'curry with one argument binded' => [
                '$numberOfArguments' => 1,
                '$function' => $function,
                '$default' => ['test'],
            ],
        ];
    }

    /**
     * @dataProvider provideCurriedSumFunction
     */
    public function test_it_should_evaluate_curried_function_if_number_of_arguments_is_fulfilled(
        callable $curriedSum
    ) {
        $this->assertSame(3, $curriedSum(1, 2));
    }

    /**
     * @dataProvider provideCurriedSumFunction
     */
    public function test_it_should_be_able_to_curry_multiple_times(
        callable $curriedSum
    ) {
        $addOne = $curriedSum(1);
        $this->assertSame(2, $addOne(1));
        $this->assertSame(3, $addOne(2));
        $this->assertSame(4, $addOne(3));
    }

    /**
     * @dataProvider provideCurriedSumFunction
     */
    public function test_it_should_be_able_to_curry_few_variants_and_evaluate_them(
        callable $curriedSum
    ) {
        $addOne = $curriedSum(1);
        $addTwo = $curriedSum(2);

        $this->assertSame(3, $addOne(2));
        $this->assertSame(4, $addTwo(2));

        $this->assertSame(6, $addOne(5));
        $this->assertSame(7, $addTwo(5));
    }

    public function provideCurriedSumFunction()
    {
        return [
            'curried sum' => [
                '$curriedSum' => f\curryN(2, function ($a, $b) {
                    return $a + $b;
                })
            ],
            'curried sum with predefined value' => [
                '$curriedSum' => f\curryN(2, function ($x, $y, $a, $b) {
                    return ($a - $x) - ($y - $b);
                }, [1, -1])
            ]
        ];
    }

    /**
     * @dataProvider provideCurriedReturnArgsFunction
     */
    public function test_it_should_be_able_to_curry_even_if_more_arguments_is_applied(
        callable $returnArgs
    ) {
        $this->assertSame([1, 2, 3], $returnArgs(1, 2, 3));
    }

    public function provideCurriedReturnArgsFunction()
    {
        return [
            'curried' => [
                '$returnArgs' => f\curryN(2, function () {
                    return func_get_args();
                })
            ]
        ];
    }

    public function test_it_should_evaluate_without_arguments_if_all_needed_were_defined()
    {
        $sum = f\curryN(0, function ($a, $b) {
            return $a + $b;
        }, [1, 2]);

        $this->assertSame(3, $sum());
    }
}
