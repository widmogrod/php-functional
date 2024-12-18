<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;

class ConcatTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_concat_values_to_array(
        $value,
        $expected
    ) {
        $this->assertEquals($expected, f\concat($value));
    }

    public static function provideData()
    {
        return [
            'list of lists' => [
                f\fromIterable([
                    f\fromIterable(['a', 1, 3]),
                    f\fromIterable(['b', 2, 4])
                ]),
                f\fromIterable(['a', 1, 3, 'b', 2, 4]),
            ],
            'list of lists of lists' => [
                f\fromIterable([
                    f\fromIterable([
                        f\fromIterable(['a', 1]),
                        f\fromIterable(['b', 2])
                    ]),
                    f\fromIterable([
                        f\fromIterable(['c', 3])
                    ]),
                ]),
                 f\fromIterable([
                    f\fromIterable(['a', 1]),
                    f\fromIterable(['b', 2]),
                    f\fromIterable(['c', 3])
                ]),
            ],
            'list of lists of lists with some noregulatives' => [
                 f\fromIterable([
                    f\fromIterable([
                        f\fromIterable(['a', 1]),
                        f\fromIterable(['b', 2]),
                    ]),
                    f\fromIterable(['c', 3])
                ]),
                 f\fromIterable([
                    f\fromIterable(['a', 1]),
                    f\fromIterable(['b', 2]),
                    'c',
                    3
                ]),
            ],
            'Just of lists' => [
                Just::of(f\fromIterable(['a', 1, 3])),
                 f\fromIterable(['a', 1, 3]),
            ],
            'Nothing of lists' => [
                Nothing::mempty(),
                 f\fromNil()
            ],
        ];
    }
}
