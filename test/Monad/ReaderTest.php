<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use Widmogrod\Monad\Reader;

class ReaderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
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

    public function provideData()
    {
        $hello = function ($x) {
            return Reader\value($x + 1);
        };
        $hi = function ($x) {
            return Reader\value($x + 2);
        };

        return [
            'reader 0' => [
                '$f' => $hello,
                '$g' => $hi,
                '$x' => 54,
                '$env' => 666,
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
        $x,
        $reader
    ) {
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

    public function provideApplicativeTestData()
    {
        return [
            'Reader' => [
                '$pure' => Reader\pure,
                '$u' => Reader\pure(function () {
                    return 1;
                }),
                '$v' => Reader\pure(function () {
                    return 5;
                }),
                '$w' => Reader\pure(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33,
                '$reader' => 3,
            ],
        ];
    }

    /**
     * @dataProvider provideFunctorTestData
     */
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x,
        $reader
    ) {
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

    public function provideFunctorTestData()
    {
        return [
            'Reader' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Reader\value(3),
                '$reader' => 'asd',
            ],
        ];
    }
}
