<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Primitive\Listt;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\head;

class HeadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_boxed_value(
        Listt $listt,
        $expected
    ) {
        $this->assertSame(
            $expected,
            head($listt)
        );
    }

    public function provideData()
    {
        return [
            'Should return head from finite array' => [
                '$listt' => fromIterable([1, 2, 3]),
                '$expected' => 1,
            ],
            'Should return head from finite iterator' => [
                '$listt' => fromIterable(new \ArrayIterator([1, 2, 3])),
                '$expected' => 1,
            ],
        ];
    }

    /**
     * @expectedException \Widmogrod\Primitive\EmptyListError
     * @expectedExceptionMessage Cannot call head() on empty list
     */
    public function test_it_should_throw_exception_when_list_is_empty()
    {
        head(fromNil());
    }
}
