<?php

namespace test\Widmogrod\Primitive;

use Widmogrod\FantasyLand\Monoid;
use Widmogrod\Functional as f;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Primitive\Product;
use Widmogrod\Primitive\Stringg;

class StringgTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideRandomizedData
     */
    public function test_it_should_obey_monoid_laws(Monoid $x, Monoid $y, Monoid $z)
    {
        MonoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $x,
            $y,
            $z
        );
    }

    /**
     * @expectedException \Widmogrod\Primitive\TypeMismatchError
     * @expectedExceptionMessage Expected type is Widmogrod\Primitive\Stringg but given Widmogrod\Primitive\Product
     * @dataProvider provideRandomizedData
     */
    public function test_it_should_reject_concat_on_different_type(Stringg $a)
    {
        $a->concat(Product::of(1));
    }

    private function randomize()
    {
        return Stringg::of(md5(random_int(0, 100)));
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
