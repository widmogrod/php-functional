<?php

/**
 * iterate :: (a -> a) -> a -> [a]
 *
 * iterate f x returns an infinite list of repeated applications of f to x:
 *  iterate f x == [x, f x, f (f x), ...]
 */
function iterate()
{
    // TODO
}

/**
 * repeat :: a -> [a]
 *
 * repeat x is an infinite list, with x the value of every element.
 */
function repeat()
{
    // TODO
}

/**
 * replicate :: Int -> a -> [a]
 *
 * replicate n x is a list of length n with x the value of every element.
 * It is an instance of the more general genericReplicate, in which n may be of any integral type.
 */
function replicate()
{
    // TODO
}

/**
 * cycle :: [a] -> [a]
 *
 * cycle ties a finite list into a circular one, or equivalently, the infinite repetition of the original list. It is the identity on infinite lists.
 */
function cycle()
{
    // TODO
}
