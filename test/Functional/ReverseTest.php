<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Widmogrod\Functional\reverse;

class ReverseTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_create_function_that_accept_args_in_reverse_order(
        callable $function,
        array    $args
    )
    {
        $reversed = reverse($function);
        $original = reverse($reversed);

        $this->assertSame(
            $original(...$args),
            $function(...$args),
            'Reverse of revert function should be the same function'
        );
        $this->assertSame(
            $reversed(...array_reverse($args)),
            $function(...$args),
            'Reversing arguments to reverse function should result in the same value as the function'
        );
    }

    public static function provideData()
    {
        return [
            'non argument function' => [
                function () {
                    return 1;
                },
                [],
            ],
            'non argument function but with args' => [
                function () {
                    return 1;
                },
                [1, 2, 3],
            ],
            'many args' => [
                function ($a, $b, $c, $d) {
                    return ($a - $c) * pow($b, $d);
                },
                [
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                ],
            ],
            'variadic args' => [
                function (...$args) {
                    return array_product($args);
                },
                [
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                    random_int(-10, 10),
                ],
            ],
        ];
    }
}
