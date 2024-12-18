<?php

declare(strict_types=1);

namespace test\Functional;

use ArgumentCountError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Widmogrod\Functional\applicator;

class ApplicatorTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_apply_value_as_a_argument_to_a_function(
        $value,
        callable $fn,
        $expected
    )
    {
        $this->assertSame(
            $expected,
            applicator($value, $fn)
        );
    }

    public function test_it_should_fail_when_function_requires_more_arguments()
    {
        $this->expectException(ArgumentCountError::class);
        $this->expectExceptionMessage('Too few arguments to function');
        applicator(1, function (int $i, string $a): int {
            return 10 + $i;
        });
    }

    public static function provideData()
    {
        return [
            'Single value function' => [
                133,
                function (int $i): int {
                    return 10 + $i;
                },
                143,
            ],
        ];
    }
}
