<?php

namespace Widmogrod\Functional;

use Widmogrod\FantasyLand\Foldable;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;
use Widmogrod\Primitive\ListtNil;

const fromIterable = 'Widmogrod\Functional\fromIterable';

class SnapshotIterator extends \IteratorIterator
{
    private $inMemoryValid;
    private $inMemoryCurrent;

    public function valid()
    {
        if (null === $this->inMemoryValid) {
            $this->inMemoryValid = parent::valid();
        }

        return $this->inMemoryValid;
    }

    public function current()
    {
        if (null === $this->inMemoryCurrent) {
            $this->inMemoryCurrent = parent::current();
        }

        return $this->inMemoryCurrent;
    }

    public function snapshot()
    {
        return new self($this->getInnerIterator());
    }
}

function fromIterable(iterable $i): Listt
{
    if (is_array($i)) {
        $i = new \ArrayObject($i);
        $i = $i->getIterator();
    }

    if (!($i instanceof SnapshotIterator)) {
        $i = new SnapshotIterator($i);
        $i->rewind();
    }

    if (!$i->valid()) {
        return new ListtNil();
    }

    $value = $i->current();
    $g = $i->snapshot();
    $g->next();

    return ListtCons::of(function () use ($g, $value) {
        return [
            $value,
            fromIterable($g)
        ];
    });
}

const fromValue = 'Widmogrod\Functional\fromValue';

function fromValue($value): Listt
{
    return ListtCons::of(function () use ($value) {
        return [$value, fromNil()];
    });
}

function fromNil(): Listt
{
    return new ListtNil();
}

/**
 * widthHeadTail :: ([x:xs] -> b) -> [a] -> b
 *
 * @param callable $fn
 * @param Listt $a
 * @return mixed
 * @throws \Widmogrod\Primitive\EmptyListError
 */
function widthHeadTail(callable $fn, Listt $a = null)
{
    return curryN(2, function (callable $fn, Listt $a) {
        return $fn(head($a), tail($a));
    })(...func_get_args());
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
    }, fromNil(), $xs);
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
        return append(fromValue($x), $xs);
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

/**
 * head :: [a] -> a
 *
 * Extract the first element of a list, which must be non-empty.
 *
 * @param Listt $l
 * @return mixed
 * @throws \Widmogrod\Primitive\EmptyListError
 */
function head(Listt $l)
{
    return $l->head();
}

/**
 * tail :: [a] -> [a]
 *
 * Extract the elements after the head of a list, which must be non-empty.
 *
 * @param Listt $l
 * @return Listt
 * @throws \Widmogrod\Primitive\EmptyListError
 */
function tail(Listt $l)
{
    return $l->tail();
}

/**
 * length :: Foldable t => t a -> Int
 *
 * Returns the size/length of a finite structure as an Int.
 * The default implementation is optimized for structures that are similar to cons-lists,
 * because there is no general way to do better.
 *
 * @param Foldable $t
 * @return int
 */
function length(Foldable $t): int
{
    return $t->reduce(function ($len) {
        return $len + 1;
    }, 0);
}
