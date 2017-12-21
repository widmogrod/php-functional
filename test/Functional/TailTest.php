<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\tail;

class TailTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_boxed_value(
        Listt $listt,
        Listt $expected
    ) {
        $this->assertTrue(tail($listt)->equals($expected));
    }

    public function provideData()
    {
        return [
            'Should return tail from finite array' => [
                '$listt' => fromIterable([1, 2, 3]),
                '$expected' => fromIterable([2, 3]),
            ],
            'Should return tail from finite iterator' => [
                '$listt' => fromIterable(new \ArrayIterator([1, 2, 3, 4, 5, 6])),
                '$expected' => fromIterable([2, 3, 4, 5, 6]),
            ],
        ];
    }

    /**
     * @expectedException \Widmogrod\Primitive\EmptyListError
     * @expectedExceptionMessage Cannot call tail() on empty list
     */
    public function test_it_should_throw_exception_when_list_is_empty()
    {
        tail(fromNil());
    }
}
