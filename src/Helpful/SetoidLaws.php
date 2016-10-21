<?php
namespace Widmogrod\Helpful;

use Widmogrod\FantasyLand\Setoid;
use Widmogrod\Functional as f;

class SetoidLaws
{
    /**
     * @param callable $assertEqual
     * @param Setoid $a
     * @param Setoid $b
     * @param Setoid $c
     */
    public static function test(
        callable $assertEqual,
        Setoid $a,
        Setoid $b,
        Setoid $c
    ) {
        $assertEqual(
            f\equal($a, $a),
            true,
            'reflexivity'
        );

        $assertEqual(
            f\equal($a, $b),
            f\equal($b, $a),
            'symmetry'
        );

        $assertEqual(
            f\equal($a, $b) && f\equal($b, $c),
            f\equal($a, $c),
            'transitivity'
        );
    }
}
