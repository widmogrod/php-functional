<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Functional as f;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use FunctionalPHP\FantasyLand\Helpful\MonoidLaws;
use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Primitive\Stringg;

class MaybeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($return, $f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            f\curryN(1, $return),
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
            [$this, 'assertEquals'],
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
     * @dataProvider provideMonoidTestData
     */
    public function test_it_should_obey_monoid_laws($x, $y, $z)
    {
        MonoidLaws::test(
            [$this, 'assertEquals'],
            $x,
            $y,
            $z
        );
    }

    public function provideMonoidTestData()
    {
        return [
            'Just' => [
                '$x' => Just::of(f\fromIterable([1])),
                '$y' => Just::of(f\fromIterable([2])),
                '$z' => Just::of(f\fromIterable([3]))
            ],
            'Nothing' => [
                '$x' => Nothing::mempty(),
                '$y' => Nothing::mempty(),
                '$z' => Nothing::mempty(),
            ],
            'Just String' => [
                '$x' => Just::of(Stringg::of("Hello")),
                '$y' => Just::of(Stringg::of(" ")),
                '$z' => Just::of(Stringg::of("World"))
            ],
            'Maybe String' => [
                '$x' => Just::of(Stringg::of("Hello")),
                '$y' => Nothing::mempty(),
                '$z' => Just::of(Stringg::of("World"))
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
            [$this, 'assertEquals'],
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
