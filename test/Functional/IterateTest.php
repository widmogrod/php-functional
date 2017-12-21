<?php

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\foldr;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\iterate;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\take;

class IterateTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = take($n, iterate(identity, $value));

            return length($list) === $n;
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

            $list = take($n, iterate($addOne, $value));

            return length(filter(eql($value), $list)) === $n
                && $value + $n === foldr(identity, $list);
        });
    }
}
