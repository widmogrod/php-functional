<?php
namespace test\Monad;

use Monad\Maybe\Just;
use Helpful\MonadLaws;

class MaybeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_maybe_monad_obeys_the_laws($f, $g, $x)
    {
        MonadLaws::test(
            [$this, 'assertEquals'],
            Just::of,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return Just::of($x + 1);
        };
        $addTwo = function ($x) {
            return Just::of($x + 2);
        };

        return [
            'state 0' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
            ],
        ];
    }
}
