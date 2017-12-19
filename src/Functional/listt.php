<?php

namespace Widmogrod\Functional;

use Widmogrod\Primitive\Listt;

function fromIterable(iterable $i): Listt
{
    return Listt::of(array_map(identity, $i));
}

///**
// * concat :: Foldable t => t [a] -> [a]
// *
// * The concatenation of all the elements of a container of lists.
// */
//function concat(Foldable $t)
//{
//    // TODO
//}

///**
// * map f xs is the list obtained by applying f to each element of xs, i.e.,
// *
// * map f [x1, x2, ..., xn] == [f x1, f x2, ..., f xn]
// * map f [x1, x2, ...] == [f x1, f x2, ...]
// */
//function map()
//{
//    // TODO
//}
//
///**
// * filter :: (a -> Bool) -> [a] -> [a] Source #
// *
// * filter, applied to a predicate and a list, returns the list of those elements that satisfy the predicate; i.e.,
// *
// * filter p xs = [ x | x <- xs, p x]
// */
//function filter()
//{
//    // TODO
//}

const prepend = 'Widmogrod\Functional\prepend';

/**
 * prepend :: a -> [a] -> [a]
 *
 * @param mixed $x
 * @param Listt $xs
 * @return Listt
 */
function prepend($x, Listt $xs = null)
{
    return curryN(2, function ($x, Listt $xs) {
        return append(fromIterable([$x]), $xs);
    })(...func_get_args());
}

const append = 'Widmogrod\Functional\append';

/**
 * (++) :: [a] -> [a] -> [a]
 *
 * Append two lists, i.e.,
 *
 *  [x1, ..., xm] ++ [y1, ..., yn] == [x1, ..., xm, y1, ..., yn]
 *  [x1, ..., xm] ++ [y1, ...] == [x1, ..., xm, y1, ...]
 *
 * If the first list is not finite, the result is the first list.
 */
function append(Listt $a, Listt $b): Listt
{
    return $a->concat($b);
}

///**
// * head :: [a] -> a
// *
// * Extract the first element of a list, which must be non-empty.
// *
// * @param Listt $l
// * @return mixed
// */
//function head(Listt $l)
//{
//    return $l->head();
//}
//
///**
// * tail :: [a] -> [a]
// *
// * Extract the elements after the head of a list, which must be non-empty.
// *
// * @param Listt $l
// * @return Listt
// */
//function tail(Listt $l)
//{
//    return $l->tail();
//}
//
///**
// * last :: [a] -> a
// *
// * Extract the last element of a list, which must be finite and non-empty.
// *
// * @param Listt $l
// */
//function last(Listt $l)
//{
//    // TODO
//}
//
///**
// * init :: [a] -> [a]
// *
// * Return all the elements of a list except the last one. The list must be non-empty.
// *
// * @param Listt $l
// */
//function init(Listt $l)
//{
//    // TODO
//}
//
//
///**
// * length :: Foldable t => t a -> Int
// *
// * Returns the size/length of a finite structure as an Int.
// * The default implementation is optimized for structures that are similar to cons-lists,
// * because there is no general way to do better.
// *
// * @param Listt $l
// * @return int
// */
//function length(Listt $l): int
//{
//    // TODO
//}
//
///**
// * (!!) :: [a] -> Int -> a infixl 9
// *
// * List index (subscript) operator, starting from 0. It is an instance of the more general genericIndex, which takes an index of any integral type.
// *
// * @param Listt $L
// */
//function index(Listt $L, int $index)
//{
//    // TODO
//}
//
///**
// * reverse :: [a] -> [a]
// *
// * reverse xs returns the elements of xs in reverse order. xs must be finite.
// *
// * @param Listt $l
// */
//function reverse(Listt $l)
//{
//    // TODO
//}
