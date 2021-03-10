<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class PipeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_compose_and_inject_input_correctly(
        $functions,
        $value,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            f\pipe($value, ...$functions)
        );
    }

    public function provideData()
    {
        return [
            'two function' => [
                '$functions' => ['strtolower', 'strtoupper'],
                '$value' => 'aBcD',
                '$expected' => 'ABCD'
            ],
        ];
    }
}
