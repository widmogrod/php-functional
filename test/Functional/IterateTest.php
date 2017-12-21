<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\iterate;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Functional\take;

class IterateTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\choose(5, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = take($n, iterate(identity, $value));

            $this->assertEquals($n, length($list));
        });
    }

    public function test_it_should_generate_repetive_value()
    {
        $this->forAll(
            Generator\choose(5, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $addOne = function (int $i): int {
                return $i + 1;
            };

            $list = take($n, iterate($addOne, $value));

            $this->assertEquals(
                $value + $n - 1,
                reduce(function ($agg, $i) {
                    return $i;
                }, 0, $list)
            );
        });
    }
}
