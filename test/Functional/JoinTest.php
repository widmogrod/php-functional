<?php

namespace test\Functional;

use Widmogrod\FantasyLand\Monad;
use function Widmogrod\Functional\flip;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\join;
use Widmogrod\Monad\Identity;
use function Widmogrod\Monad\Maybe\just;
use Widmogrod\Monad\State;

class JoinTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_remove_one_level_of_monadic_structure(
        Monad $monad,
        callable $run,
        $expected
    ) {
        $result = join($monad);
        $this->assertEquals($expected, $run($result));
    }

    public function provideData()
    {
        return [
            'Just (Just 1)' => [
                '$monad' => just(just(1)),
                '$run' => identity,
                '$value' => just(1),
            ],
            'Identity (Identity 1)' => [
                '$monad' => Identity::of(Identity::of(2)),
                '$run' => identity,
                '$value' => Identity::of(2),
            ],
            'State (State 1)' => [
                '$monad' => State\put(State\put(3)),
                '$run' => flip(State\execState, 0),
                '$value' => State\put(3),
            ],
        ];
    }
}
