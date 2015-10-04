<?php
namespace test\Monad;

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
}
