<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Functional as f;

class CurryTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideFunctionToCurry')]
    public function test_it_should_detect_automatically_number_of_arguments_to_curry(
        $fn,
        $resultAfterCurries
    ) {
        $times = 0;
        $curried = f\curry($fn);

        do {
            $curried = $curried(1);
            $times++;
            // Prevents from infinite loop
            $this->assertLessThanOrEqual($resultAfterCurries, $times, 'Curried to more times than expected');
        } while (is_callable($curried));

        $this->assertEquals(
            $resultAfterCurries,
            $times,
            'Function was curried wrong number of times'
        );
    }

    public static function provideFunctionToCurry()
    {
        return [
            'args = 0' => [
                function () {
                },
                1,
            ],
            'args = 1' => [
                function ($a) {
                },
                1,
            ],
            'args = 2' => [
                function ($a, $b) {
                },
                2,
            ],
            'args = 2 with default' => [
                function ($a, $b = null) {
                },
                2,
            ],
        ];
    }

    #[DataProvider('provideFunctionsToCurry')]
    public function test_it_curry_with_lest_arguments_if_defaults_are_provided(
        $result,
        $function
    ) {
        $this->assertEquals(
            $result,
            $function()
        );
    }

    public static function provideFunctionsToCurry()
    {
        return [
            'curry args = 0 and default = 0' => [
                null,
                f\curry(function () {
                }, [])
            ],
            'curry args = 1 and default = 1' => [
                [1],
                f\curry(function ($a) {
                    return [$a];
                }, [1])
            ],
            'curry args = 2 and default = 2' => [
                [1, 2],
                f\curry(function ($a, $b) {
                    return [$a, $b];
                }, [1, 2])
            ],
            'curry args = 2 and default = 3' => [
                [1, 2, 3],
                f\curry(function ($a) {
                    return func_get_args();
                }, [1, 2, 3])
            ],
            'curry args = 2 and default = 4' => [
                [1, 2],
                f\curry(function ($a, $b) {
                    return [$a, $b];
                }, [1, 2, 3, 4])
            ],
        ];
    }

    #[DataProvider('provideCallablesToTest')]
    public function test_it_curry_every_type_of_callable(callable $callable)
    {
        $curried = f\curry($callable);

        $this->assertInstanceOf(\Closure::class, $curried);
        $this->assertInstanceOf(\Closure::class, $curried(1));
        $this->assertSame([1, 2], $curried(1)(2));
    }

    public static function provideCallablesToTest()
    {
        return [
            'closure' => [
                function ($a, $b) {
                    return [$a, $b];
                }
            ],
            'named function' => [
                'test\Functional\inner\my_named_function'
            ],
            'static method and static context' => [
                [inner\MyClass::class, 'myStaticMethod']
            ],
            'static method and object context' => [
                [new inner\MyClass(), 'myStaticMethod']
            ],
            'method and object context' => [
                [new inner\MyClass(), 'myMethod']
            ],
            'static method as string' => [
                'test\Functional\inner\MyClass::myStaticMethod'
            ],
            'invokable object' => [
                new inner\InvokableClass()
            ]
        ];
    }

    public static function staticMethod($a, $b)
    {
        return [$a, $b];
    }
}

namespace test\Functional\inner;

function my_named_function($a, $b)
{
    return [$a, $b];
}

class MyClass
{
    public function myMethod($a, $b)
    {
        return [$a, $b];
    }

    public static function myStaticMethod($a, $b)
    {
        return [$a, $b];
    }
}

class InvokableClass
{
    public function __invoke($a, $b)
    {
        return [$a, $b];
    }
}
