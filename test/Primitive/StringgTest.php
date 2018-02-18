<?php

declare(strict_types=1);

namespace test\Widmogrod\Primitive;

use Eris\Generator;
use Eris\TestTrait;
use FunctionalPHP\FantasyLand\Helpful\MonoidLaws;
use FunctionalPHP\FantasyLand\Helpful\SetoidLaws;
use Widmogrod\Primitive\Product;
use Widmogrod\Primitive\Stringg;

class StringgTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_obay_setoid_laws()
    {
        $this->forAll(
            Generator\char(),
            Generator\string(),
            Generator\names()
        )->then(function (string $x, string $y, string $z) {
            SetoidLaws::test(
                [$this, 'assertEquals'],
                Stringg::of($x),
                Stringg::of($y),
                Stringg::of($z)
            );
        });
    }

    public function test_it_should_obey_monoid_laws()
    {
        $this->forAll(
            Generator\char(),
            Generator\string(),
            Generator\names()
        )->then(function (string $x, string $y, string $z) {
            MonoidLaws::test(
                [$this, 'assertEquals'],
                Stringg::of($x),
                Stringg::of($y),
                Stringg::of($z)
            );
        });
    }

    /**
     * @expectedException \Widmogrod\Primitive\TypeMismatchError
     * @expectedExceptionMessage Expected type is Widmogrod\Primitive\Stringg but given Widmogrod\Primitive\Product
     */
    public function test_it_should_reject_concat_on_different_type()
    {
        $this->forAll(
            Generator\string(),
            Generator\int()
        )->then(function (string $x, int $y) {
            Stringg::of($x)->concat(Product::of($y));
        });
    }
}
