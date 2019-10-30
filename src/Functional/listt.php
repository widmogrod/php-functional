<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

use FunctionalPHP\FantasyLand\Foldable;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;
use Widmogrod\Useful\SnapshotIterator;

/**
 * @var callable
 */
const fromIterable = 'Widmogrod\Functional\fromIterable';

/**
 * fromIterable :: iterable a -> [a]
 *
 * Adapt any native PHP value that is iterable into Listt.
 *
 * @param  iterable $i
 * @return Listt
 */
function fromIterable(iterable $i): Listt
{
    if (is_array($i)) {
        $i = new \ArrayObject($i);
    }

    if ($i instanceof \IteratorAggregate) {
        $i = $i->getIterator();
    }

    $i = new SnapshotIterator($i);
    $i->rewind();

    return fromSnapshotIterator($i);
}

/**
 * Utility function. Must not be used directly.
 * Use fromValue() or fromIterable()
 *
 * @param  SnapshotIterator $i
 * @return Listt
 */
function fromSnapshotIterator(SnapshotIterator $i): Listt
{
    if (!$i->valid()) {
        return fromNil();
    }

    return new ListtCons(function () use ($i) {
        return [
            $i->current(),
            fromSnapshotIterator($i->snapshot())
        ];
    });
}

/**
 * @var callable
 */
const fromValue = 'Widmogrod\Functional\fromValue';

/**
 * fromValue :: a -> [a]
 *
 * Create list containing only one value.
 *
 * @param  mixed $value
 * @return Listt
 */
function fromValue($value): Listt
{
    return ListtCons::of($value);
}

/**
 * @var callable
 */
const fromNil = 'Widmogrod\Functional\fromNil';

function fromNil(): Listt
{
    return ListtCons::mempty();
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
function concat(Foldable $xs): Listt
{
    return foldr(function (Foldable $x, Listt $y) {
        return foldr(prepend, $y, $x);
    }, fromNil(), $xs);
}

/**
 * @var callable
 */
const prepend = 'Widmogrod\Functional\prepend';

/**
 * prepend :: a -> [a] -> [a]
 *
 * @param  mixed          $x
 * @param  Listt          $xs
 * @return Listt|\Closure
 */
function prepend($x, Listt $xs = null)
{
    return curryN(2, function ($x, Listt $xs): Listt {
        return new ListtCons(function () use ($x, $xs) {
            return [$x, $xs];
        });
    })(...func_get_args());
}

/**
 * @var callable
 */
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
 * @param  Listt          $a
 * @param  Listt|null     $b
 * @return Listt|callable
 */
function append(Listt $a, Listt $b = null)
{
    return curryN(2, function (Listt $a, Listt $b): Listt {
        return $a->concat($b);
    })(...func_get_args());
}

/**
 * @var callable
 */
const head = 'Widmogrod\Functional\head';

/**
 * head :: [a] -> a
 *
 * Extract the first element of a list, which must be non-empty.
 *
 * @param  Listt                               $l
 * @return mixed
 * @throws \Widmogrod\Primitive\EmptyListError
 */
function head(Listt $l)
{
    return $l->head();
}

/**
 * @var callable
 */
const tail = 'Widmogrod\Functional\tail';

/**
 * tail :: [a] -> [a]
 *
 * Extract the elements after the head of a list, which must be non-empty.
 *
 * @param  Listt                               $l
 * @return Listt
 * @throws \Widmogrod\Primitive\EmptyListError
 */
function tail(Listt $l)
{
    return $l->tail();
}

/**
 * @var callable
 */
const length = 'Widmogrod\Functional\length';

/**
 * length :: Foldable t => t a -> Int
 *
 * Returns the size/length of a finite structure as an Int.
 * The default implementation is optimized for structures that are similar to cons-lists,
 * because there is no general way to do better.
 *
 * @param  Foldable $t
 * @return int
 */
function length(Foldable $t): int
{
    return $t->reduce(function ($len) {
        return $len + 1;
    }, 0);
}
