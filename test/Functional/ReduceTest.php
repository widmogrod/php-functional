<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class ReduceTest extends \PHPUnit\Framework\TestCase
{
    public function test_reduce()
    {
        $list = f\fromIterable([1, 2, 3, 4]);

        $result = f\reduce(function ($accumulator, $value) {
            return f\concatM($accumulator, f\fromIterable([$value + 1]));
        }, f\fromNil(), $list);

        $this->assertEquals(
            $result,
            f\fromIterable([2, 3, 4, 5])
        );
    }
}
