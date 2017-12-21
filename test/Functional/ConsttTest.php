<?php

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use function Widmogrod\Functional\constt;

class ConsttTest extends \PHPUnit_Framework_TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\int(),
            Generator\int()
        )->then(function ($a, $b) {
            return constt($a, $b) === $a
                && constt($a)($b) === $a;
        });
    }
}
