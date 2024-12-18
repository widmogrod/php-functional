<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use FunctionalPHP\FantasyLand\Helpful\MonoidLaws;
use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Primitive\Stringg;

class MaybeTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
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

    public static function provideData()
    {
        return [
            'Just' => [
                Just::of,
                function ($x) {
                    return Just::of($x + 1);
                },
                function ($x) {
                    return Just::of($x + 2);
                },
                10,
            ],
            'Nothing' => [
                Nothing::of,
                function ($x) {
                    return Nothing::of($x + 1);
                },
                function ($x) {
                    return Nothing::of($x + 2);
                },
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
    )
    {
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

    public static function provideApplicativeTestData()
    {
        return [
            'Just' => [
                Maybe\pure,
                Just::of(function () {
                    return 1;
                }),
                Just::of(function () {
                    return 5;
                }),
                Just::of(function () {
                    return 7;
                }),
                function ($x) {
                    return $x + 400;
                },
                33
            ],
            'Nothing' => [
                Maybe\pure,
                Nothing::of(function () {
                    return 1;
                }),
                Nothing::of(function () {
                    return 5;
                }),
                Nothing::of(function () {
                    return 7;
                }),
                function ($x) {
                    return $x + 400;
                },
                33
            ],
        ];
    }

    #[DataProvider('provideMonoidTestData')]
    public function test_it_should_obey_monoid_laws($x, $y, $z)
    {
        MonoidLaws::test(
            [$this, 'assertEquals'],
            $x,
            $y,
            $z
        );
    }

    public static function provideMonoidTestData()
    {
        return [
            'Just' => [
                Just::of(f\fromIterable([1])),
                Just::of(f\fromIterable([2])),
                Just::of(f\fromIterable([3]))
            ],
            'Nothing' => [
                Nothing::mempty(),
                Nothing::mempty(),
                Nothing::mempty(),
            ],
            'Just String' => [
                Just::of(Stringg::of("Hello")),
                Just::of(Stringg::of(" ")),
                Just::of(Stringg::of("World"))
            ],
            'Maybe String' => [
                Just::of(Stringg::of("Hello")),
                Nothing::mempty(),
                Just::of(Stringg::of("World"))
            ],
        ];
    }

    #[DataProvider('provideFunctorTestData')]
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor  $x
    )
    {
        FunctorLaws::test(
            [$this, 'assertEquals'],
            $f,
            $g,
            $x
        );
    }

    public static function provideFunctorTestData()
    {
        return [
            'Just' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Just::of(1),
            ],
            'Nothing' => [
                function ($x) {
                    return $x + 1;
                },
                function ($x) {
                    return $x + 5;
                },
                Nothing::of(1),
            ],
        ];
    }
}
