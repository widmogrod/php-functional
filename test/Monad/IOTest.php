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
use Widmogrod\Monad\IO;

class IOTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_if_io_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            function (IO $f, IO $g, $message) {
                $this->assertEquals(
                    $f->run(),
                    $g->run(),
                    $message
                );
            },
            function ($x) {
                return IO::of(function () use ($x) {
                    return $x;
                });
            },
            $f,
            $g,
            $x
        );
    }

    public static function provideData()
    {
        $addOne = function ($x) {
            return IO::of(function () use ($x) {
                return $x + time() + 1;
            });
        };
        $addTwo = function ($x) {
            return IO::of(function () use ($x) {
                return $x + time() + 2;
            });
        };

        return [
            'Identity' => [
                $addOne,
                $addTwo,
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
    )
    {
        ApplicativeLaws::test(
            function (IO $a, IO $b, $message) {
                $this->assertEquals(
                    $a->run(),
                    $b->run(),
                    $message
                );
            },
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
            'IO' => [
                IO\pure,
                IO\pure(function () {
                    return 1;
                }),
                IO\pure(function () {
                    return 5;
                }),
                IO\pure(function () {
                    return 7;
                }),
                function ($x) {
                    return 400 + $x;
                },
                33
            ],
        ];
    }

    #[DataProvider('provideFunctorTestData')]
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor  $x
    )
    {
        FunctorLaws::test(
            function (IO $a, IO $b, $message) {
                $this->assertEquals(
                    $a->run(),
                    $b->run(),
                    $message
                );
            },
            $f,
            $g,
            $x
        );
    }

    public static function provideFunctorTestData()
    {
        return [
            'IO' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                IO::of(function () {
                    return 1;
                }),
            ],
        ];
    }
}
