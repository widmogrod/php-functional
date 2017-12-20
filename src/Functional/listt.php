<?php

namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Foldable;
use Widmogrod\Primitive\Listt;

const fromIterable = 'Widmogrod\Functional\fromIterable';

function fromIterable(iterable $i): Listt
{
    return Listt::of($i);
}

const fromValue = 'Widmogrod\Functional\fromValue';

function fromValue($value): Listt
{
    return fromIterable([$value]);
}

/**
 * @var callable
 */
const concat = 'Widmogrod\Functional\concat';

/**
 * concat :: Foldable t => t [a] -> [a]
 *
 * <code>
 * concat(fromIterable([fromIterable([1, 2]), fromIterable([3, 4])])) == fromIterable([1, 2, 3, 4])
 * </code>
 *
 * concat :: Foldable t => t [a] -> [a]
 * concat xs = build (\c n -> foldr (\x y -> foldr c y x) n xs)
 *
 * build :: forall a. (forall b. (a -> b -> b) -> b -> b) -> [a]
 * build g = g (:) []
 *
 * foldr :: (a -> b -> b) -> b -> [a] -> b
 *
 * The concatenation of all the elements of a container of lists.
 *
 * @param Foldable $xs
 *
 * @return Listt
 */
function concat(Foldable $xs)
{
    return foldr(function ($x, Listt $y) {
        return foldr(prepend, $y, $x);
    }, Listt::mempty(), $xs);
}

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
    return curryN(2, function ($x, Listt $xs): Listt {
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
 *
 * @param Listt $a
 * @param Listt|null $b
 * @return Listt|callable
 */
function append(Listt $a, Listt $b = null)
{
    return curryN(2, function (Listt $a, Listt $b): Listt {
        return $a->concat($b);
    })(...func_get_args());
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
