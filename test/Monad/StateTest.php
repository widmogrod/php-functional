<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
use Monad\State;
use Helpful\MonadLaws;

class StateTest extends \PHPUnit_Framework_TestCase
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
        ApplicativeInterface $u,
        ApplicativeInterface $v,
        ApplicativeInterface $w,
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
            'State' => [
                '$pure' => State\pure,
                '$u' => State\pure(function ($x) {
                    return 1 + $x;
                }),
                '$v' => State\pure(function ($x) {
                    return 5 + $x;
                }),
                '$w' => State\pure(function ($x) {
                    return 7 + $x;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33,
                '$state' => 3,
            ],
        ];
    }
}
