<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Either;
use Widmogrod\Monad\Either\Left;
use Widmogrod\Monad\Either\Right;

class EitherTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            f\curryN(1, $return),
            $f,
            $g,
            $x
        );
    }

    public static function provideData()
    {
        return [
            'Right' => [
                Right::of,
                function ($x) {
                    return Right::of($x + 1);
                },
                function ($x) {
                    return Right::of($x + 2);
                },
                10,
            ],
            // I don't know if Left should be tested?
            'Left' => [
                Left::of,
                function ($x) {
                    return Left::of($x);
                },
                function ($x) {
                    return Left::of($x);
                },
                10,
            ],
        ];
    }

    #[DataProvider('provideApplicativeTestData')]
    public function test_it_should_obey_applicative_laws(
        $pure,
        Applicative $u,
        Applicative $v,
        Applicative $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            [$this, 'assertEquals'],
            f\curryN(1, $pure),
            $u,
            $v,
            $w,
            $f,
            $x
        );
    }

    public static function provideApplicativeTestData()
    {
        return [
            'Right' => [
                Either\pure,
                Right::of(function () {
                    return 1;
                }),
                Right::of(function () {
                    return 5;
                }),
                Right::of(function () {
                    return 7;
                }),
                function ($x) {
                    return $x + 400;
                },
                33
            ],
            'Left' => [
                Either\pure,
                Left::of(function () {
                    return 1;
                }),
                Left::of(function () {
                    return 5;
                }),
                Left::of(function () {
                    return 7;
                }),
                function ($x) {
                    return $x + 400;
                },
                33
            ],
        ];
    }

    #[DataProvider('provideFunctorTestData')]
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x
    ) {
        FunctorLaws::test(
            [$this, 'assertEquals'],
            $f,
            $g,
            $x
        );
    }

    public static function provideFunctorTestData()
    {
        return [
            'Right' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Right::of(1),
            ],
            'Left' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Left::of(1),
            ],
        ];
    }
}
