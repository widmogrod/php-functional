<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use function Widmogrod\Functional\constt;

class ConsttTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\int(),
            Generator\int()
        )->then(function ($a, $b) {
            $this->assertEquals($a, constt($a, $b));
            $this->assertEquals($a, constt($a)($b));
        });
    }
}
