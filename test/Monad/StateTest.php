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
use Widmogrod\Monad\State;

class StateTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_if_state_monad_obeys_the_laws($f, $g, $x, $state)
    {
        MonadLaws::test(
            function (State $a, State $b, $message) use ($state) {
                $this->assertEquals(
                    $a->runState($state),
                    $b->runState($state),
                    $message
                );
            },
            State\value,
            $f,
            $g,
            $x
        );
    }

    public static function provideData()
    {
        $addOne = function ($x) {
            return State\value($x + 1);
        };
        $addTwo = function ($x) {
            return State\value($x + 2);
        };

        return [
            'state 0' => [
                $addOne,
                $addTwo,
                10,
                0,
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
        $state
    )
    {
        ApplicativeLaws::test(
            function (State $a, State $b, $message) use ($state) {
                $this->assertEquals(
                    $a->runState($state),
                    $b->runState($state),
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
            'State' => [
                State\pure,
                State\pure(function () {
                    return 1;
                }),
                State\pure(function ($x) {
                    return 5;
                }),
                State\pure(function ($x) {
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
                 $state
    )
    {
        FunctorLaws::test(
            function (State $a, State $b, $message) use ($state) {
                $this->assertEquals(
                    $a->runState($state),
                    $b->runState($state),
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
            'State' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                State\value(3),
                'asd',
            ],
        ];
    }
}
