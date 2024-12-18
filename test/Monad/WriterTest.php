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
use Widmogrod\Monad\Writer;

class WriterTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_if_writer_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            function (Writer $a, Writer $b, $message) {
                $this->assertEquals(
                    $a->runWriter(),
                    $b->runWriter(),
                    $message
                );
            },
            Writer\pure,
            $f,
            $g,
            $x
        );
    }

    public static function provideData()
    {
        $addOne = function ($x) {
            return Writer::of($x + 1);
        };
        $addTwo = function ($x) {
            return Writer::of($x + 2);
        };

        return [
            'writer 0' => [
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
    ) {
        ApplicativeLaws::test(
            function (Writer $a, Writer $b, $message) {
                $this->assertEquals(
                    $a->runWriter(),
                    $b->runWriter(),
                    $message
                );
            },
            $pure,
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
            'Writer' => [
                Writer\pure,
                Writer\pure(function () {
                    return 1;
                }),
                Writer\pure(function () {
                    return 5;
                }),
                Writer\pure(function () {
                    return 7;
                }),
                function ($x) {
                    return 400 + $x;
                },
                33,
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
            function (Writer $a, Writer $b, $message) {
                $this->assertEquals(
                    $a->runWriter(),
                    $b->runWriter(),
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
            'Writer' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Writer\pure(3),
            ],
        ];
    }
}
