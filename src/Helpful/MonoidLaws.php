<?php
namespace Widmogrod\Helpful;

use Widmogrod\FantasyLand\Monoid;
use Widmogrod\Functional as f;

class MonoidLaws
{
    /**
     * Generic test to verify if a type obey the monodic laws.
     *
     * @param callable $assertEqual Asserting function (Monoid $m1, Monoid $m2, $message)
     * @param Monoid $x
     * @param Monoid $y
     * @param Monoid $z
     */
    public static function test(
        callable $assertEqual,
        Monoid $x,
        Monoid $y,
        Monoid $z
    ) {
        // Make callable string invokable as a function $fn()
        $assertEqual = f\curryN(3, $assertEqual);

        $assertEqual(
            f\mappend($x, f\mempty($x)),
            $x,
            'Right identity'
        );
        $assertEqual(
            f\mappend(f\mempty($x), $x),
            $x,
            'Left identity'
        );

        $assertEqual(
            f\mappend($x, f\mappend($y, $z)),
            f\mappend(f\mappend($x, $y), $z),
            'Associativity'
        );
    }
}
