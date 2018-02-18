<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use Widmogrod\Monad\Writer;

class WriterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
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

    public function provideData()
    {
        $addOne = function ($x) {
            return Writer::of($x + 1);
        };
        $addTwo = function ($x) {
            return Writer::of($x + 2);
        };

        return [
            'writer 0' => [
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

    public function provideApplicativeTestData()
    {
        return [
            'Writer' => [
                '$pure' => Writer\pure,
                '$u' => Writer\pure(function () {
                    return 1;
                }),
                '$v' => Writer\pure(function () {
                    return 5;
                }),
                '$w' => Writer\pure(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33,
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

    public function provideFunctorTestData()
    {
        return [
            'Writer' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Writer\pure(3),
            ],
        ];
    }
}
