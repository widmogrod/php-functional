<?php

declare(strict_types=1);

namespace test\Functional;

use FunctionalPHP\FantasyLand\Monad;
use Widmogrod\Common\ValueOfInterface;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Either;
use Widmogrod\Monad\IO;
use Widmogrod\Monad\Maybe;

class LiftM2Test extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider monadsProvider
     */
    public function test_it_should_lift2M(
        Monad $ma,
        Monad $mb,
        callable $transformation,
        string $expectedFQCN,
        callable $valueAssertion = null
    ) {
        $mc = f\liftM2($transformation, $ma, $mb);

        $this->assertInstanceOf($expectedFQCN, $mc);
        if ($valueAssertion !== null) {
            $valueAssertion($mc);
        }
    }

    public function monadsProvider()
    {
        $sumIntegers = static function (int $a, int $b) {
            return $a + $b;
        };

        $sameValueOf = f\curryN(2, function ($expected, ValueOfInterface $actual) {
            $this->assertSame($expected, f\valueOf($actual));
        });

        return [
            'maybe all nothing' => [
                Maybe\nothing(),
                Maybe\nothing(),
                $sumIntegers,
                Maybe\Nothing::class
            ],
            'maybe first just' => [
                Maybe\just(1),
                Maybe\nothing(),
                $sumIntegers,
                Maybe\Nothing::class
            ],
            'maybe second just' => [
                Maybe\nothing(),
                Maybe\just(2),
                $sumIntegers,
                Maybe\Nothing::class
            ],
            'maybe all just' => [
                Maybe\just(1),
                Maybe\just(2),
                $sumIntegers,
                Maybe\Just::class,
                $sameValueOf(3)
            ],
            'either all left' => [
                Either\left('a'),
                Either\left('b'),
                $sumIntegers,
                Either\Left::class,
                $sameValueOf('a')
            ],
            'either first right' => [
                Either\right(3),
                Either\left('b'),
                $sumIntegers,
                Either\Left::class,
                $sameValueOf('b')
            ],
            'either second right' => [
                Either\left('a'),
                Either\right(4),
                $sumIntegers,
                Either\Left::class,
                $sameValueOf('a')
            ],
            'either all right' => [
                Either\right(3),
                Either\right(4),
                $sumIntegers,
                Either\Right::class,
                $sameValueOf(7)
            ],
            'io' => [
                IO::of(function () {
                    return 1;
                }),
                IO::of(function () {
                    return 2;
                }),
                $sumIntegers,
                IO::class,
                function (IO $io) {
                    $this->assertSame(3, $io->run());
                }
            ]
        ];
    }
}
