<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Functional as f;

class TeeTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_be_curried(
        $function,
        $value
    )
    {
        $curried = f\tee($function);
        $this->assertEquals($value, $curried($value));
    }

    #[DataProvider('provideData')]
    public function test_it_should_return_input_value(
        $function,
        $value
    )
    {
        $this->assertEquals($value, f\tee($function, $value));
    }

    public static function provideData()
    {
        return [
            'add two' => [
                function ($v) {
                    return 2 + $v;
                },
                1,
            ],
        ];
    }
}
