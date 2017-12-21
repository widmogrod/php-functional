<?php

namespace test\Primitive;

use Widmogrod\Functional as f;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Helpful\SetoidLaws;
use Widmogrod\Primitive\Product;
use Widmogrod\Primitive\Sum;

class SumTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideRandomizedData
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
     * @dataProvider provideRandomizedData
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

    /**
     * @expectedException \Widmogrod\Primitive\TypeMismatchError
     * @expectedExceptionMessage Expected type is Widmogrod\Primitive\Sum but given Widmogrod\Primitive\Product
     * @dataProvider provideRandomizedData
     */
    public function test_it_should_reject_concat_on_different_type(Sum $a)
    {
        $a->concat(Product::of(1));
    }

    private function randomize()
    {
        return Sum::of(random_int(-100000000, 100000000));
    }

    public function provideRandomizedData()
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
