<?php
namespace test\Monad;

use Monad\State;
use Helpful\MonadLaws;

class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_if_state_monad_obeys_the_laws($f, $g, $x, $state)
    {
        MonadLaws::test(
            function ($a, $b, $err) use ($state) {
                $this->assert($a->run($state) == $b->run($state), $err);
            },
            State\value,
            $f,
            $g,
            $x
        );
    }

    public function provideData()
    {
        $addOne = function ($x) {
            return State\value($x + 1);
        };
        $addTwo = function ($x) {
            return State\value($x + 2);
        };

        return [
            'state 0' => [
                '$f' => $addOne,
                '$g' => $addTwo,
                '$x' => 10,
                '$state' => 0,
            ],
        ];
    }
}
