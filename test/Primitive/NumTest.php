<?php

declare(strict_types=1);

namespace test\Primitive;

use Eris\TestTrait;
use Eris\Generator;
use FunctionalPHP\FantasyLand\Helpful\SetoidLaws;
use Widmogrod\Primitive\Num;

class NumTest extends \PHPUnit\Framework\TestCase
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
                Num::of($x),
                Num::of($y),
                Num::of($z)
            );
        });
    }
}
