<?php

namespace test\Functional;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

class ReduceTest extends \PHPUnit_Framework_TestCase
{
    public function test_reduce()
    {
        $list = Listt::of([1, 2, 3, 4]);

        $result = f\reduce(function ($accumulator, $value) {
            return f\concatM($accumulator, Listt::of([$value + 1]));
        }, Listt::of([]), $list);

        $this->assertEquals(
            $result,
            Listt::of([2, 3, 4, 5])
        );
    }
}
