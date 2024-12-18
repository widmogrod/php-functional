<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Monad\Identity;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\valueOf;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

class ValueOfTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_return_boxed_value(
        $value,
        $expected
    ) {
        $this->assertSame(
            $expected,
            valueOf($value)
        );
    }

    public static function provideData()
    {
        return [
            'Native int should be return as is' => [
                1023,
               1023,
            ],
            'Native string should be return as is' => [
                'Something amazing',
               'Something amazing',
            ],
            'Native array should be return as is' => [
                [1, 2, 3],
               [1, 2, 3],
            ],
            'Identity 6' => [
                Identity::of(6),
               6
            ],
            'Just 6' => [
                just(6),
               6
            ],
            'Nothing' => [
                nothing(),
               null
            ],
            'Listt' => [
                fromIterable([1, 2, 3]),
               [1, 2, 3]
            ],
        ];
    }
}
