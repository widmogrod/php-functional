<?php
namespace Helpful;

use FantasyLand\Functor;
use Functional as f;

class FunctorLaws
{
    /**
     * Generic test to verify if a type obey the functor laws.
     *
     * @param callable $assertEqual Asserting function (Functor $f1, Functor $f2, $message)
     * @param callable $f   (a -> b)
     * @param callable $g   (a -> b)
     * @param Functor $x    f a
     */
    public static function test(
        callable $assertEqual,
        callable $f,
        callable $g,
        Functor $x
    ) {
        // Make callable string invokable as a function $fn()
        $assertEqual = f\curryN(3, $assertEqual);

        // identity: fmap id  ==  id
        $assertEqual(
            f\map(f\identity, $x),
            $x,
            'identity'
        );

        // composition: fmap (f . g)  ==  fmap f . fmap g
        $assertEqual(
            f\map(f\compose($f, $g), $x),
            call_user_func(
                f\compose(f\map($f), f\map($g)),
                $x
            ),
            'composition'
        );
    }
}
