<?php

declare(strict_types=1);

namespace test\Monad;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Helpful\ApplicativeLaws;
use FunctionalPHP\FantasyLand\Helpful\FunctorLaws;
use FunctionalPHP\FantasyLand\Helpful\MonadLaws;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\Identity;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Free\liftF;

class FreeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideFunctorTestData
     */
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

    public function provideFunctorTestData()
    {
        return [
            'Pure' => [
                '$f' => function (int $x) {
                    return $x + 1;
                },
                '$g' => function (int $x) {
                    return $x + 5;
                },
                '$x' => Pure::of(1),
            ],
            'Free' => [
                '$f' => function (int $x) {
                    return $x + 1;
                },
                '$g' => function (int $x) {
                    return $x + 5;
                },
                '$x' => liftF(Pure::of(1)),
            ],
        ];
    }

    /**
     * @dataProvider provideMonadTestData
     */
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

    public function provideMonadTestData()
    {
        $addOne = function (int $x) {
            return Pure::of($x + 1);
        };
        $addTwo = function (int $x) {
            return Pure::of($x + 2);
        };

        return [
            'Identity' => [
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

    public function provideApplicativeTestData()
    {
        return [
            'Pure' => [
                '$pure' => Pure::of,
                '$u' => Pure::of(function () {
                    return 1;
                }),
                '$v' => Pure::of(function () {
                    return 5;
                }),
                '$w' => Pure::of(function () {
                    return 7;
                }),
                '$f' => function ($x) {
                    return 400 + $x;
                },
                '$x' => 33
            ],
        ];
    }
}
