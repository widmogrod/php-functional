<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Functional as f;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use Widmogrod\Monad\State;

class StateTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
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

    public function provideData()
    {
        $addOne = function ($x) {
            return State\value($x + 1);
        };
        $addTwo = function ($x) {
            return State\value($x + 2);
        };

        return [
            'state 0' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
                '$state' => 0,
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
        $state
    ) {
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

    public function provideApplicativeTestData()
    {
        return [
            'State' => [
                '$pure' => State\pure,
                '$u' => State\pure(function () {
                    return 1;
                }),
                '$v' => State\pure(function ($x) {
                    return 5;
                }),
                '$w' => State\pure(function ($x) {
                    return 7;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33,
                '$state' => 3,
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
        $state
    ) {
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

    public function provideFunctorTestData()
    {
        return [
            'State' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => State\value(3),
                '$state' => 'asd',
            ],
        ];
    }
}
