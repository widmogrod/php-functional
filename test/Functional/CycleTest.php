<?php

declare(strict_types=1);

namespace test\Functional;

use Eris\Generator;
use Eris\TestTrait;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\cycle;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\iterate;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\take;

class CycleTest extends \PHPUnit\Framework\TestCase
{
    use TestTrait;

    public function test_it_should_generate_infinite_list()
    {
        $this->forAll(
            Generator\choose(2, 100),
            Generator\int()
        )->then(function ($n, $value) {
            $list = cycle(take($n, iterate(identity, $value)));
            $list = take($n * 2, $list);

            $this->assertEquals($n * 2, length($list));
        });
    }

    public function test_it_should_generate_repetitive_value()
    {
        $list = cycle(fromIterable(['a', 1]));
        $list = take(4, $list);

        $this->assertEquals(
            ['a', 1, 'a', 1],
            $list->extract()
        );
    }
}
