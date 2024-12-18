<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\tail;

class TailTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_return_boxed_value(
        Listt $listt,
        Listt $expected
    ) {
        $this->assertTrue(tail($listt)->equals($expected));
    }

    public static function provideData()
    {
        return [
            'Should return tail from finite array' => [
                fromIterable([1, 2, 3]),
               fromIterable([2, 3]),
            ],
            'Should return tail from finite iterator' => [
                fromIterable(new \ArrayIterator([1, 2, 3, 4, 5, 6])),
               fromIterable([2, 3, 4, 5, 6]),
            ],
        ];
    }

    public function test_it_should_throw_exception_when_list_is_empty()
    {
        $this->expectException(\Widmogrod\Primitive\EmptyListError::class);
        $this->expectExceptionMessage('Cannot call tail() on empty list');
        tail(fromNil());
    }
}
