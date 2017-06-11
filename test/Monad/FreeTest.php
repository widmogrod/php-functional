<?php

namespace test\Monad;

use Widmogrod\FantasyLand\Functor;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Helpful\MonadLaws;
use function Widmogrod\Monad\Free\liftF;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use const Widmogrod\Functional\identity;
use Widmogrod\Monad\Identity;

class FreeTest extends \PHPUnit_Framework_TestCase
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
                    $a->runFree(identity),
                    $b->runFree(identity),
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
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => Pure::of(1),
            ],
            'Free' => [
                '$f' => function ($x) {
                    return $x + 1;
                },
                '$g' => function ($x) {
                    return $x + 5;
                },
                '$x' => liftF(Identity::of(1)),
            ],
        ];
    }

    /**
     * @dataProvider provideData
     */
    public function test_if_io_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            function (MonadFree $f, MonadFree $g, $message) {
                $this->assertEquals(
                    $f->runFree(identity),
                    $g->runFree(identity),
                    $message
                );
            },
            function ($x) {
                return Pure::of($x);
            },
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return liftF(Identity::of(
                $x + 1
            ));
        };
        $addTwo = function ($x) {
            return liftF(Identity::of(
                $x + 2
            ));
        };

        return [
            'Identity' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
            ],
        ];
    }
}
