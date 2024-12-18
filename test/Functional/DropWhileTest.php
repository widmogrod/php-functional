<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\dropWhile;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\lt;

class DropWhileTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it(
        Listt $a,
        callable $fn,
        Listt $expected
    ) {
        $result = dropWhile($fn, $a);

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
                lt(3),
                fromNil(),
            ],
            'should provided list when < 100' => [
                fromIterable([1, 2, 3, 4, 5]),
                lt(100),
                fromIterable([]),
            ],
            'should return part of finite list' => [
                fromIterable([1, 2, 3, 4, 5]),
                lt(4),
                fromIterable([4, 5]),
            ],
            'should return nil list when drop more than in the list' => [
                fromIterable([1, 2, 3, 4, 5]),
                lt(400),
                fromNil(),
            ],
        ];
    }
}
