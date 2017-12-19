<?php

/**
 * take :: Int -> [a] -> [a]
 *
 * take n, applied to a list xs, returns the prefix of xs of length n, or xs itself if n > length xs:
 */
function take()
{
    // TODO
}

/**
 * drop :: Int -> [a] -> [a]
 *
 * drop n xs returns the suffix of xs after the first n elements, or [] if n > length xs:
 */
function drop()
{
    // TODO
}

/**
 * splitAt :: Int -> [a] -> ([a], [a])
 *
 * splitAt n xs returns a tuple where first element is xs prefix of length n and second element is the remainder of the list:
 */
function splitAt()
{
    // TODO
}

/**
 * takeWhile :: (a -> Bool) -> [a] -> [a]
 *
 * takeWhile, applied to a predicate p and a list xs, returns the longest prefix (possibly empty) of xs of elements that satisfy p:
 */
function takeWhile()
{
    // TODO
}

/**
 * dropWhile :: (a -> Bool) -> [a] -> [a]
 *
 * dropWhile p xs returns the suffix remaining after takeWhile p xs:
 */
function dropWhile()
{
    // TODO
}

/**
 * span :: (a -> Bool) -> [a] -> ([a], [a])
 *
 * span, applied to a predicate p and a list xs, returns a tuple where first element is longest prefix (possibly empty) of xs of elements that satisfy p and second element is the remainder of the list:
 */
function span()
{
    // TODO
}

/**
 * break :: (a -> Bool) -> [a] -> ([a], [a])
 *
 * break, applied to a predicate p and a list xs, returns a tuple where first element is longest prefix (possibly empty) of xs of elements that do not satisfy p and second element is the remainder of the list:
 */
function breakk()
{
    // TODO
}
