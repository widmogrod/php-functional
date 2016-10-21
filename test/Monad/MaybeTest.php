<?php
namespace test\Monad;

use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\Helpful\ApplicativeLaws;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Helpful\MonadLaws;
use Widmogrod\Functional as f;

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $return,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        return [
            'Just' => [
                '$return' => Just::of,
                '$f' => function ($x) {
                    return Just::of($x + 1);
                },
                '$g' => function ($x) {
                    return Just::of($x + 2);
                },
                '$x' => 10,
            ],
            'Nothing' => [
                '$return' => Nothing::of,
                '$f' => function ($x) {
                    return Nothing::of($x + 1);
                },
                '$g' => function ($x) {
                    return Nothing::of($x + 2);
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
        Applicative $u,
        Applicative $v,
        Applicative $w,
        callable $f,
        $x
    ) {
        ApplicativeLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
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
            'Just' => [
                '$pure' => Maybe\pure,
                '$u' => Just::of(function () {
                    return 1;
                }),
                '$v' => Just::of(function () {
                    return 5;
                }),
                '$w' => Just::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
                },
                '$x' => 33
            ],
            'Nothing' => [
                '$pure' => Maybe\pure,
                '$u' => Nothing::of(function () {
                    return 1;
                }),
                '$v' => Nothing::of(function () {
                    return 5;
                }),
                '$w' => Nothing::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return $x + 400;
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
            f\curryN(3, [$this, 'assertEquals']),
            $f,
            $g,
            $x
        );
    }

    public function provideFunctorTestData()
    {
        return [
            'Just' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Just::of(1),
            ],
            'Nothing' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Nothing::of(1),
            ],
        ];
    }
}
