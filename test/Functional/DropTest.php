<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\drop;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;

class DropTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it(
        Listt $a,
        int $n,
        Listt $expected
    ) {
        $result = drop($n, $a);

        $r = print_r($result->extract(), true);
        $e = print_r($expected->extract(), true);

        $this->assertTrue(
            $result->equals($expected),
            "$e != $r"
        );
    }

    public static function provideData()
    {
        return [
            'should return empty list from when input is empty list' => [
                fromNil(),
                1,
                fromNil(),
            ],
            'should provided list when n is zero' => [
                fromIterable([1, 2, 3, 4, 5]),
                0,
                fromIterable([1, 2, 3, 4, 5]),
            ],
            'should provided list when n is negative' => [
                fromIterable([1, 2, 3, 4, 5]),
                random_int(-1000, -1),
                fromIterable([1, 2, 3, 4, 5]),
            ],
            'should return part of finite list' => [
                fromIterable([1, 2, 3, 4, 5]),
                3,
                fromIterable([4, 5]),
            ],
            'should return nil list when drop more than in the list' => [
                fromIterable([1, 2, 3, 4, 5]),
                3000,
                fromNil(),
            ],
        ];
    }
}
