<?php

namespace test\Functional;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

class FoldrTest extends \PHPUnit_Framework_TestCase
{
    public function test_foldr()
    {
        $list = Listt::of([1, 2, 3, 4]);

        $result = f\foldr(function ($accumulator, $value) {
            return f\concatM($accumulator, Listt::of([$value + 1]));
        }, Listt::of([]), $list);

        $this->assertEquals(
            $result,
            Listt::of([5, 4, 3, 2])
        );
    }
}
