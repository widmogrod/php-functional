<?php

namespace test\Primitive;

use Eris\TestTrait;
use Widmogrod\Functional as f;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Helpful\SetoidLaws;
use Widmogrod\Primitive\Product;
use Widmogrod\Primitive\Stringg;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

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

    /**
     * @expectedException \Widmogrod\Primitive\TypeMismatchError
     * @expectedExceptionMessage Expected type is Widmogrod\Primitive\Product but given Widmogrod\Primitive\Stringg
     * @dataProvider provideSetoidLaws
     */
    public function test_it_should_reject_concat_on_different_type(Product $a)
    {
        $a->concat(Stringg::of("a"));
    }

    private function randomize()
    {
        usleep(100);

        return Product::of(random_int(-1000, 1000));
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
