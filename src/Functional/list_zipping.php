<?php

/**
 * zip :: [a] -> [b] -> [(a, b)]
 *
 * zip takes two lists and returns a list of corresponding pairs. If one input list is short, excess elements of the longer list are discarded.
 * zip is right-lazy:
 */
function zip()
{
    // TODO
}

/**
 * unzip :: [(a, b)] -> ([a], [b])
 *
 * unzip transforms a list of pairs into a list of first components and a list of second components.
 */
function unzip()
{
    // TODO
}


/**
 * zipWith :: (a -> b -> c) -> [a] -> [b] -> [c]
 *
 * zipWith generalises zip by zipping with the function given as the first argument, instead of a tupling function. For example, zipWith (+) is applied to two lists to produce the list of corresponding sums.
 * zipWith is right-lazy:
 */
function zipWith()
{
    // TODO
}
