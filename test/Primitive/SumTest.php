<?php

namespace test\Primitive;

use Widmogrod\Functional as f;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Helpful\SetoidLaws;
use Widmogrod\Primitive\Sum;

class SumTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideSetoidLaws
     */
    public function test_it_should_obay_setoid_laws(
        $a,
        $b,
        $c
    ) {
        SetoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $a,
            $b,
            $c
        );
    }

    /**
     * @dataProvider provideSetoidLaws
     */
    public function test_it_should_obay_monoid_laws(
        $a,
        $b,
        $c
    ) {
        MonoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $a,
            $b,
            $c
        );
    }

    private function randomize()
    {
        return Sum::of(random_int(-100000000, 100000000));
    }

    public function provideSetoidLaws()
    {
        return array_map(function () {
            return [
                $this->randomize(),
                $this->randomize(),
                $this->randomize(),
            ];
        }, array_fill(0, 50, null));
    }
}
