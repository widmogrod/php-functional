<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\Listt;

/**
 * iterate :: (a -> a) -> a -> [a]
 *
 * iterate f x returns an infinite list of repeated applications of f to x:
 *  iterate f x == [x, f x, f (f x), ...]
 */
function iterate(): Listt
{
    // TODO
}

/**
 * repeat :: a -> [a]
 *
 * repeat x is an infinite list, with x the value of every element.
 */
function repeat(): Listt
{
    // TODO
}

/**
 * replicate :: Int -> a -> [a]
 *
 * replicate n x is a list of length n with x the value of every element.
 * It is an instance of the more general genericReplicate, in which n may be of any integral type.
 */
function replicate(): Listt
{
    // TODO
}

/**
 * cycle :: [a] -> [a]
 *
 * cycle ties a finite list into a circular one, or equivalently, the infinite repetition of the original list. It is the identity on infinite lists.
 */
function cycle(Listt $l): Listt
{
    // TODO
}
