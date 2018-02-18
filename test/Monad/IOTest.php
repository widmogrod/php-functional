<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Functional as f;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use Widmogrod\Monad\IO;

class IOTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
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

    public function provideData()
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
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
            ],
        ];
    }

    /**
     * @dataProvider provideApplicativeTestData
     */
    public function test_it_should_obey_applicative_laws(
        $pure,
        Applicative $u,
        Applicative $v,
        Applicative $w,
        callable $f,
        $x
    ) {
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

    public function provideApplicativeTestData()
    {
        return [
            'IO' => [
                '$pure' => IO\pure,
                '$u' => IO\pure(function () {
                    return 1;
                }),
                '$v' => IO\pure(function () {
                    return 5;
                }),
                '$w' => IO\pure(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33
            ],
        ];
    }

    /**
     * @dataProvider provideFunctorTestData
     */
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x
    ) {
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

    public function provideFunctorTestData()
    {
        return [
            'IO' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => IO::of(function () {
                    return 1;
                }),
            ],
        ];
    }
}
