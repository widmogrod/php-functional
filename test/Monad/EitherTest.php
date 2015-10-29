<?php
namespace test\Monad;

use FantasyLand\ApplicativeInterface;
use Helpful\ApplicativeLaws;
use Monad\Either;
use Monad\Either\Left;
use Monad\Either\Right;
use Helpful\MonadLaws;

class EitherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            $return,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        return [
            'Right' => [
                '$return' => Right::of,
                '$f' => function ($x) {
                    return Right::of($x + 1);
                },
                '$g' => function ($x) {
                    return Right::of($x + 2);
                },
                '$x' => 10,
            ],
            // I don't know if Left should be tested?
            'Left' => [
                '$return' => Left::of,
                '$f' => function ($x) {
                    return Left::of($x);
                },
                '$g' => function ($x) {
                    return Left::of($x);
                },
                '$x' => 10,
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
        $x
    ) {
        ApplicativeLaws::test(
            [$this, 'assertEquals'],
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
            'Right' => [
                '$pure' => Either\pure,
                '$u' => Right::of(function () {
                    return 1;
                }),
                '$v' => Right::of(function () {
                    return 5;
                }),
                '$w' => Right::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
            'Left' => [
                '$pure' => Either\pure,
                '$u' => Left::of(function () {
                    return 1;
                }),
                '$v' => Left::of(function () {
                    return 5;
                }),
                '$w' => Left::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
        ];
    }
}
