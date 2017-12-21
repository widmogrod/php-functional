<?php

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use function Widmogrod\Functional\eql;
use function Widmogrod\Functional\filter;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\replicate;

class ReplicateTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = replicate($n, $value);

            return length($list) === $n;
        });
    }

    public function test_it_should_generate_repetive_value()
    {
        $this->forAll(
            Generator\choose(1, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = replicate($n, $value);

            return length(filter(eql($value), $list)) === $n;
        });
    }
}
