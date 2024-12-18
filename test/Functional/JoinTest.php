<?php

declare(strict_types=1);

namespace test\Functional;

use FunctionalPHP\FantasyLand\Monad;
use PHPUnit\Framework\Attributes\DataProvider;
use Widmogrod\Monad\Identity;
use Widmogrod\Monad\State;
use const Widmogrod\Functional\identity;
use function Widmogrod\Functional\flip;
use function Widmogrod\Functional\join;
use function Widmogrod\Monad\Maybe\just;

class JoinTest extends \PHPUnit\Framework\TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_remove_one_level_of_monadic_structure(
        Monad $monad,
        callable $run,
        $expected
    ) {
        $result = join($monad);
        $this->assertEquals($expected, $run($result));
    }

    public static function provideData()
    {
        return [
            'Just (Just 1)' => [
                just(just(1)),
                identity,
                just(1),
            ],
            'Identity (Identity 1)' => [
                Identity::of(Identity::of(2)),
                identity,
                Identity::of(2),
            ],
            'State (State 1)' => [
                State\put(State\put(3)),
                flip(State\execState, 0),
                State\put(3),
            ],
        ];
    }
}
