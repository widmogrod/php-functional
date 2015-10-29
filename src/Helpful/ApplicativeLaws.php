<?php
namespace Helpful;

use FantasyLand\ApplicativeInterface;
use Functional as f;

class ApplicativeLaws
{
    /**
     * Generic test to verify if a monad obey the laws.
     *
     * @param callable $assertEqual   Asserting function (Applicative $a1, Applicative $a2, $message)
     * @param callable $pure          Applicative "constructor"
     * @param ApplicativeInterface $u Applicative f => f (a -> b)
     * @param ApplicativeInterface $v Applicative f => f (a -> b)
     * @param ApplicativeInterface $w Applicative f => f (a -> b)
     * @param callable $f             (a -> b)
     * @param mixed $x                Value to put into a applicative
     */
    public static function test(
        callable $assertEqual,
        callable $pure,
        ApplicativeInterface $u,
        ApplicativeInterface $v,
        ApplicativeInterface $w,
        callable $f,
        $x
    ) {
        // Make callable string invokable as a function $fn()
        $assertEqual = f\curryN(3, $assertEqual);
        $pure = f\curryN(1, $pure);

        // identity: pure id <*> v = v
        $assertEqual(
            $pure(f\identity)->ap($v),
            $v,
            'identity'
        );

        // homomorphism: pure f <*> pure x = pure (f x)
        $assertEqual(
            $pure($f)->ap($pure($x)),
            $pure($f($x)),
            'homomorphism'
        );

        // interchange: u <*> pure y = pure ($ y) <*> u
        $assertEqual(
            $u->ap($pure($f)),
            $pure(f\applicator($f))->ap($u),
            'interchange'
        );

        // composition: pure (.) <*> u <*> v <*> w = u <*> (v <*> w)
        $compose = f\curryN(2, f\compose);
        $assertEqual(
            $pure($compose)->ap($u)->ap($v)->ap($w),
            $u->ap($v->ap($w)),
            'composition'
        );
    }
}
