<?php

namespace test\Functional;

use Widmogrod\Functional as f;

class TeeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_be_curried(
        $function,
        $value
    ) {
        $curried = f\tee();
        $curried = $curried($function);
        $this->assertEquals($value, $curried($value));
    }

    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_input_value(
        $function,
        $value
    ) {
        $this->assertEquals($value, f\tee($function, $value));
    }

    public function provideData()
    {
        return [
            'add two' => [
                '$function' => function ($v) {
                    return 2 + $v;
                },
                '$value' => 1,
            ],
        ];
    }
}
