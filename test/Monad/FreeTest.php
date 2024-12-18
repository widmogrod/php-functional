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
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\Identity;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Free\liftF;
use const Widmogrod\Functional\identity;

class FreeTest extends TestCase
{
    #[DataProvider('provideFunctorTestData')]
    public function test_it_should_obey_functor_laws(
        callable $f,
        callable $g,
        Functor $x
    ) {
        FunctorLaws::test(
            function (MonadFree $a, MonadFree $b, $message) {
                $this->assertEquals(
                    foldFree(Identity::of, $a, Identity::of),
                    foldFree(Identity::of, $b, Identity::of),
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
            'Pure' => [
                function (int $x) {
                    return $x + 1;
                },
                function (int $x) {
                    return $x + 5;
                },
                Pure::of(1),
            ],
            'Free' => [
                function (int $x) {
                    return $x + 1;
                },
                function (int $x) {
                    return $x + 5;
                },
                liftF(Pure::of(1)),
            ],
        ];
    }

    #[DataProvider('provideMonadTestData')]
    public function test_it_should_obey_monad_laws($f, $g, $x)
    {
        MonadLaws::test(
            function (MonadFree $f, MonadFree $g, $message) {
                $this->assertEquals(
                    foldFree(Identity::of, $f, Identity::of),
                    foldFree(Identity::of, $g, Identity::of),
                    $message
                );
            },
            Pure::of,
            $f,
            $g,
            $x
        );
    }

    public static function provideMonadTestData()
    {
        $addOne = function (int $x) {
            return Pure::of($x + 1);
        };
        $addTwo = function (int $x) {
            return Pure::of($x + 2);
        };

        return [
            'Identity' => [
                $addOne,
                $addTwo,
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
    ) {
        ApplicativeLaws::test(
            function (MonadFree $a, MonadFree $b, $message) {
                $this->assertEquals(
                    foldFree(identity, $a, Identity::of),
                    foldFree(identity, $b, Identity::of),
                    $message
                );
            },
            curryN(1, $pure),
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
            'Pure' => [
                Pure::of,
                Pure::of(function () {
                    return 1;
                }),
                Pure::of(function () {
                    return 5;
                }),
                Pure::of(function () {
                    return 7;
                }),
                function ($x) {
                    return 400 + $x;
                },
                33
            ],
        ];
    }
}
