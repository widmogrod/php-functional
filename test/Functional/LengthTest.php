<?php

declare(strict_types=1);

namespace test\Functional;

use FunctionalPHP\FantasyLand\Foldable;
use PHPUnit\Framework\Attributes\DataProvider;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\length;

class LengthTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_return_boxed_value(
        Foldable $t,
        int $expected
    ) {
        $this->assertEquals(length($t), ($expected));
    }

    public static function provideData()
    {
        return [
            'Empty list should have length 0' => [
                fromNil(),
                0,
            ],

            'Finite list should have length' => [
                fromIterable([1, 2, 3, 4]),
                4,
            ],
        ];
    }
}
