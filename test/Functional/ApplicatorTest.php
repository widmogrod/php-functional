<?php

declare(strict_types=1);

namespace test\Functional;

use function Widmogrod\Functional\applicator;

class ApplicatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_apply_value_as_a_argument_to_a_function(
        $value,
        callable $fn,
        $expected
    ) {
        $this->assertSame(
            $expected,
            applicator($value, $fn)
        );
    }

    /**
     * @expectedException \ArgumentCountError
     * @expectedExceptionMessage Too few arguments to function
     */
    public function test_it_should_fail_when_function_requires_more_argumetns()
    {
        applicator(1, function (int $i, string $a): int {
            return 10 + $i;
        });
    }

    public function provideData()
    {
        return [
            'Single value function' => [
                '$value' => 133,
                '$fn' => function (int $i): int {
                    return 10 + $i;
                },
                '$expected' => 143,
            ],
        ];
    }
}
