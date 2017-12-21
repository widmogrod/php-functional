<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class ComposeTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_retun_function_accepting_arguments()
    {
        $this->assertInstanceOf(\Closure::class, f\compose('strtolower', 'strtoupper'));
    }

    /**
     * @dataProvider provideData
     */
    public function test_it_should_be_curried(
        $functions,
        $value,
        $expected
    ) {
        $fn = f\compose(...$functions);
        $this->assertEquals(
            $expected,
            $fn($value)
        );
    }

    public function provideData()
    {
        return [
            'two function' => [
                '$functions' => ['strtolower', 'strtoupper'],
                '$value' => 'aBcD',
                '$expected' => 'abcd'
            ],
        ];
    }
}
