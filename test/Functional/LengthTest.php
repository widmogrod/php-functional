<?php

declare(strict_types=1);

namespace test\Functional;

use FunctionalPHP\FantasyLand\Foldable;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\length;

class LengthTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_boxed_value(
        Foldable $t,
        int $expected
    ) {
        $this->assertEquals(length($t), ($expected));
    }

    public function provideData()
    {
        return [
            'Empty list should have length 0' => [
                '$t' => fromNil(),
                '$expected' => 0,
            ],

            'Finite list should have length' => [
                '$t' => fromIterable([1, 2, 3, 4]),
                '$expected' => 4,
            ],
        ];
    }
}
