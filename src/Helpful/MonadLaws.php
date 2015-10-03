<?php
namespace Helpful;

use Functional as f;

class MonadLaws
{
    /**
     * Generic test to verify if a monad obey the laws.
     *
     * @param callable $assertEqual Asserting function (Monad $m1, Monad $m2, $message)
     * @param callable $return      Monad "constructor"
     * @param callable $f           Monadic function
     * @param callable $g           Monadic function
     * @param mixed $x              Value to put into a monad
     */
    public static function test(
        callable $assertEqual,
        callable $return,
        callable $f,
        callable $g,
        $x
    ) {
        // Make callable string invokable as a function $fn()
        $return = f\curryN(1, $return);
        $assertEqual = f\curryN(3, $assertEqual);

        // Make reading bellow tests easier
        $m = $return($x);

        // left identity: (return x) >>= f ≡ f x
        $assertEqual(f\bind($f, $return($x)), $f($x), 'left identity');

        // right identity: m >>= return ≡ m
        $assertEqual(f\bind($return, $m), $m, 'right identity');

        // associativity: (m >>= f) >>= g ≡ m >>= ( \x -> (f x >>= g) )
        $assertEqual(
            f\bind($g, f\bind($f, $m)),
            f\bind(function ($x) use ($f, $g) {
                return f\bind($g, $f($x));
            }, $m),
            'associativity'
        );
    }
}
