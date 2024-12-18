<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class FoldrTest extends TestCase
{
    public function test_foldr()
    {
        $list = f\fromIterable([1, 2, 3, 4]);

        $result = f\foldr(function ($value, $accumulator) {
            return f\concatM($accumulator, f\fromIterable([$value + 1]));
        }, f\fromNil(), $list);

        $this->assertEquals(
            $result,
            f\fromIterable([5, 4, 3, 2])
        );
    }
}
