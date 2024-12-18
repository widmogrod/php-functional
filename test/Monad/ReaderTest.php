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
use Widmogrod\Monad\Reader;

class ReaderTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_if_reader_monad_obeys_the_laws($f, $g, $x, $env)
    {
        MonadLaws::test(
            function (Reader $a, Reader $b, $message) use ($env) {
                $this->assertEquals(
                    $a->runReader($env),
                    $b->runReader($env),
                    $message
                );
            },
            Reader\value,
            $f,
            $g,
            $x
        );
    }

    public static function provideData()
    {
        $hello = function ($x) {
            return Reader\value($x + 1);
        };
        $hi = function ($x) {
            return Reader\value($x + 2);
        };

        return [
            'reader 0' => [
                $hello,
                $hi,
                54,
                666,
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
        $x,
        $reader
    )
    {
        ApplicativeLaws::test(
            function (Reader $a, Reader $b, $message) use ($reader) {
                $this->assertEquals(
                    $a->runReader($reader),
                    $b->runReader($reader),
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
            'Reader' => [
                Reader\pure,
                Reader\pure(function () {
                    return 1;
                }),
                Reader\pure(function () {
                    return 5;
                }),
                Reader\pure(function () {
                    return 7;
                }),
                function ($x) {
                    return 400 + $x;
                },
                33,
                3,
            ],
        ];
    }

    #[DataProvider('provideFunctorTestData')]
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor  $x,
                 $reader
    )
    {
        FunctorLaws::test(
            function (Reader $a, Reader $b, $message) use ($reader) {
                $this->assertEquals(
                    $a->runReader($reader),
                    $b->runReader($reader),
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
            'Reader' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Reader\value(3),
                'asd',
            ],
        ];
    }
}
