<?php

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\foldr;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\cycle;
use function Widmogrod\Functional\iterate;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\take;

class CycleTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = cycle(take($n, iterate(identity, $value)));
            $list = take($n * 2, $list);

            return length($list) === $n * 2;
        });
    }

    public function test_it_should_generate_repetive_value()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $addOne = function (int $i): int {
                return $i + 1;
            };

            $list = cycle(take($n, iterate($addOne, $value)));
            $list = take($n * 2, $list);

            return length(filter(eql($value), $list)) === $n
                &&  ($value + $n) * 2 === foldr(identity, $list);
        });
    }
}
