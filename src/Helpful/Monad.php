<?php
namespace Helpful;

use Functional as f;

class Monad
{
    public static function testMonadLaws(callable $assert, callable $return, callable $f, callable $g, $x)
    {
        $return = f\curryN(1, $return); // make callable string invokable as a function $fn()
        $m = $return($x);

        // left identity: (return x) >>= f ≡ f x
        $assert(f\bind($f, $return($x)) == $f($x), 'left identity');

        // right identity: m >>= return ≡ m
        $assert(f\bind($return, $m) == $m, 'right identity');

        // associativity: (m >>= f) >>= g ≡ m >>= ( \x -> (f x >>= g) )
        $assert(f\bind($g, f\bind($f, $m))
            == f\bind(function ($x) use ($f, $g) {
                return f\bind($g, $f($x));
            }, $m), 'associativity');
    }
}