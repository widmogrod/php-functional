<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;

class ConcatTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_concat_values_to_array(
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\concat($value));
    }

    public function provideData()
    {
        return [
            'list of lists' => [
                '$array' => f\fromIterable([
                    f\fromIterable(['a', 1, 3]),
                    f\fromIterable(['b', 2, 4])
                ]),
                '$expected' => f\fromIterable(['a', 1, 3, 'b', 2, 4]),
            ],
            'list of lists of lists' => [
                '$array' => f\fromIterable([
                    f\fromIterable([
                        f\fromIterable(['a', 1]),
                        f\fromIterable(['b', 2])
                    ]),
                    f\fromIterable([
                        f\fromIterable(['c', 3])
                    ]),
                ]),
                '$expected' => f\fromIterable([
                    f\fromIterable(['a', 1]),
                    f\fromIterable(['b', 2]),
                    f\fromIterable(['c', 3])
                ]),
            ],
            'list of lists of lists with some noregulatives' => [
                '$array' => f\fromIterable([
                    f\fromIterable([
                        f\fromIterable(['a', 1]),
                        f\fromIterable(['b', 2]),
                    ]),
                    f\fromIterable(['c', 3])
                ]),
                '$expected' => f\fromIterable([
                    f\fromIterable(['a', 1]),
                    f\fromIterable(['b', 2]),
                    'c',
                    3
                ]),
            ],
            'Just of lists' => [
                '$array' => Just::of(f\fromIterable(['a', 1, 3])),
                '$expected' => f\fromIterable(['a', 1, 3]),
            ],
            'Nothing of lists' => [
                '$array' => Nothing::mempty(),
                '$expected' => f\fromNil()
            ],
        ];
    }
}
