<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Monad\Identity;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\valueOf;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

class ValueOfTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_boxed_value(
        $value,
        $expected
    ) {
        $this->assertSame(
            $expected,
            valueOf($value)
        );
    }

    public function provideData()
    {
        return [
            'Native int should be return as is' => [
                '$value' => 1023,
                '$expected' => 1023,
            ],
            'Native string should be return as is' => [
                '$value' => 'Something amazing',
                '$expected' => 'Something amazing',
            ],
            'Native array should be return as is' => [
                '$value' => [1, 2, 3],
                '$expected' => [1, 2, 3],
            ],
            'Identity 6' => [
                '$value' => Identity::of(6),
                '$expected' => 6
            ],
            'Just 6' => [
                '$value' => just(6),
                '$expected' => 6
            ],
            'Nothing' => [
                '$value' => nothing(),
                '$expected' => null
            ],
            'Listt' => [
                '$value' => fromIterable([1, 2, 3]),
                '$expected' => [1, 2, 3]
            ],
        ];
    }
}
