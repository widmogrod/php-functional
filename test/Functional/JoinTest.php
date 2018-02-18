<?php

declare(strict_types=1);

namespace test\Functional;

use FunctionalPHP\FantasyLand\Monad;
use Widmogrod\Monad\Identity;
use Widmogrod\Monad\State;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\flip;
use function Widmogrod\Functional\join;
use function Widmogrod\Monad\Maybe\just;

class JoinTest extends \PHPUnit\Framework\TestCase
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
