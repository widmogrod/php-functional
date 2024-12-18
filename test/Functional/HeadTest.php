<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\head;

class HeadTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_return_boxed_value(
        Listt $listt,
        $expected
    ) {
        $this->assertSame(
            $expected,
            head($listt)
        );
    }

    public static function provideData()
    {
        return [
            'Should return head from finite array' => [
                fromIterable([1, 2, 3]),
                1,
            ],
            'Should return head from finite iterator' => [
                fromIterable(new \ArrayIterator([1, 2, 3])),
                1,
            ],
        ];
    }

    public function test_it_should_throw_exception_when_list_is_empty()
    {
        $this->expectException(\Widmogrod\Primitive\EmptyListError::class);
        $this->expectExceptionMessage('Cannot call head() on empty list');
        head(fromNil());
    }
}
