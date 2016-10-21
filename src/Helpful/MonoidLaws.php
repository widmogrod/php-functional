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
        $assertEqual(
            f\concatM($x, f\emptyM($x)),
            $x,
            'Right identity'
        );
        $assertEqual(
            f\concatM(f\emptyM($x), $x),
            $x,
            'Left identity'
        );

        $assertEqual(
            f\concatM($x, f\concatM($y, $z)),
            f\concatM(f\concatM($x, $y), $z),
            'Associativity'
        );
    }
}
