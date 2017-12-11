<?php

namespace test\Monad;

use Widmogrod\FantasyLand\Functor;
use Widmogrod\Helpful\FunctorLaws;
use Widmogrod\Helpful\MonadLaws;
use Widmogrod\Monad\Free2\MonadFree;
use Widmogrod\Monad\Free2\Pure;
use Widmogrod\Monad\Identity;
use function Widmogrod\Monad\Free2\foldFree;
use function Widmogrod\Monad\Free2\liftF;

class Free2Test extends \PHPUnit_Framework_TestCase
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
     * @dataProvider provideData
     */
    public function test_if_io_monad_obeys_the_laws($f, $g, $x)
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

    public function provideData()
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
}
