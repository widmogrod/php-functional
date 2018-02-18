<?php

declare(strict_types=1);

namespace test\Primitive;

use Eris\TestTrait;
use Eris\Generator;
use FunctionalPHP\FantasyLand\Helpful\MonoidLaws;
use FunctionalPHP\FantasyLand\Helpful\SetoidLaws;
use Widmogrod\Primitive\Product;
use Widmogrod\Primitive\Sum;

class SumTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_obay_setoid_laws()
    {
        $this->forAll(
            Generator\choose(0, 1000),
            Generator\choose(1000, 4000),
            Generator\choose(4000, 100000)
        )->then(function (int $x, int $y, int $z) {
            SetoidLaws::test(
                [$this, 'assertEquals'],
                Sum::of($x),
                Sum::of($y),
                Sum::of($z)
            );
        });
    }

    public function test_it_should_obay_monoid_laws()
    {
        $this->forAll(
            Generator\choose(0, 1000),
            Generator\choose(1000, 4000),
            Generator\choose(4000, 100000)
        )->then(function (int $x, int $y, int $z) {
            MonoidLaws::test(
                [$this, 'assertEquals'],
                Sum::of($x),
                Sum::of($y),
                Sum::of($z)
            );
        });
    }


    /**
     * @expectedException \Widmogrod\Primitive\TypeMismatchError
     * @expectedExceptionMessage Expected type is Widmogrod\Primitive\Sum but given Widmogrod\Primitive\Product
     */
    public function test_it_should_reject_concat_on_different_type()
    {
        $this->forAll(
            Generator\int(),
            Generator\string()
        )->then(function (int $x, string $y) {
            Sum::of($x)->concat(Product::of($y));
        });
    }
}
