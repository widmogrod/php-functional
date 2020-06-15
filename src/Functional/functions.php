<?php

declare(strict_types=1);

namespace Widmogrod\Functional;

use FunctionalPHP\FantasyLand\Applicative;
use FunctionalPHP\FantasyLand\Foldable;
use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Monad;
use FunctionalPHP\FantasyLand\Traversable;
use Widmogrod\Common\ValueOfInterface;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\ListtCons;

/**
 * @var callable
 */
const applicator = 'Widmogrod\Functional\applicator';

/**
 * applicator :: a -> (a -> b) -> b
 *
 * @param mixed    $x
 * @param callable $f
 *
 * @return mixed
 */
function applicator($x, callable $f = null)
{
    return curryN(2, function ($y, callable $f) {
        return $f($y);
    })(...func_get_args());
}

/**
 * @var callable
 */
const invoke = 'Widmogrod\Functional\invoke';

/**
 * invoke :: a -> #{a: (_ -> b)} -> b
 *
 * @param string $method
 * @param mixed  $object
 *
 * @return mixed
 */
function invoke($method, $object = null)
{
    return curryN(2, function ($method, $object) {
        return $object->$method();
    })(...func_get_args());
}

/**
 * Curry function
 *
 * @param int      $numberOfArguments
 * @param callable $function
 * @param array    $args
 *
 * @return callable
 */
function curryN($numberOfArguments, callable $function, array $args = [])
{
    return function (...$argsNext) use ($numberOfArguments, $function, $args) {
        $argsLeft = $numberOfArguments - func_num_args();

        return $argsLeft <= 0
            ? $function(...push_($args, $argsNext))
            : curryN($argsLeft, $function, push_($args, $argsNext));
    };
}

/**
 * Curry function
 *
 * @param callable $function
 * @param array    $args
 *
 * @return callable
 */
function curry(callable $function, array $args = [])
{
    $reflectionOfFunction = new \ReflectionFunction(\Closure::fromCallable($function));

    $numberOfArguments = count($reflectionOfFunction->getParameters());
    // We cant expect more arguments than are defined in function
    // So if some arguments are provided on start, umber of arguments to curry should be reduces
    $numberOfArguments -= count($args);

    return curryN($numberOfArguments, $function, $args);
}

/**
 * @var callable
 */
const valueOf = 'Widmogrod\Functional\valueOf';

/**
 * Retrieve value of a object
 *
 * @param ValueOfInterface|mixed $value
 *
 * @return mixed
 */
function valueOf($value)
{
    return $value instanceof ValueOfInterface
        ? $value->extract()
        : $value;
}

/**
 * @var callable
 */
const tee = 'Widmogrod\Functional\tee';

/**
 * Call $function with $value and return $value
 *
 * @param callable $function
 * @param mixed    $value
 *
 * @return \Closure
 */
function tee(callable $function, $value = null)
{
    return curryN(2, function (callable $function, $value) {
        $function($value);

        return $value;
    })(...func_get_args());
}

/**
 * @var callable
 */
const reverse = 'Widmogrod\Functional\reverse';

/**
 * reverse :: (a -> b -> c -> d) -> (c -> b -> a -> d)
 *
 * Call $function with arguments in reversed order
 *
 * @return \Closure
 *
 * @param callable $function
 */
function reverse(callable $function)
{
    return function (...$args) use ($function) {
        return $function(...array_reverse($args));
    };
}

/**
 * @var callable
 */
const map = 'Widmogrod\Functional\map';

/**
 * map :: Functor f => (a -> b) -> f a -> f b
 *
 * @return mixed|\Closure
 *
 * @param callable $transformation
 * @param Functor  $value
 */
function map(callable $transformation, Functor $value = null)
{
    return curryN(2, function (callable $transformation, Functor $value) {
        return $value->map($transformation);
    })(...func_get_args());
}

/**
 * @var callable
 */
const bind = 'Widmogrod\Functional\bind';

/**
 * bind :: Monad m => (a -> m b) -> m a -> m b
 *
 * @return mixed|\Closure
 *
 * @param callable $function
 * @param Monad    $value
 */
function bind(callable $function, Monad $value = null)
{
    return curryN(2, function (callable $function, Monad $value) {
        return $value->bind($function);
    })(...func_get_args());
}

/**
 * @var callable
 */
const join = 'Widmogrod\Functional\join';

/**
 * join :: Monad m => m (m a) -> m a
 *
 * @param Monad $monad
 *
 * @return Monad
 */
function join(Monad $monad): Monad
{
    return $monad->bind(identity);
}

/**
 * @var callable
 */
const reduce = 'Widmogrod\Functional\reduce';

/**
 * reduce :: Foldable t => (b -> a -> b) -> b -> t a -> b
 *
 * @param callable $callable    Binary function ($accumulator, $value)
 * @param mixed    $accumulator
 * @param Foldable $foldable
 *
 * @return mixed
 */
function reduce(callable $callable, $accumulator = null, Foldable $foldable = null)
{
    return curryN(3, function (
        callable $callable,
        $accumulator,
        Foldable $foldable
    ) {
        return $foldable->reduce($callable, $accumulator);
    })(...func_get_args());
}

/**
 * @var callable
 */
const foldr = 'Widmogrod\Functional\foldr';

/**
 * foldr :: Foldable t => (a -> b -> b) -> b -> t a -> b
 *
 * Foldr is expresed by foldl (reduce) so it loose some properties.
 * For more reading please read this article https://wiki.haskell.org/Foldl_as_foldr
 *
 * @param callable $callable    Binary function ($value, $accumulator)
 * @param mixed    $accumulator
 * @param Foldable $foldable
 *
 * @return mixed
 */
function foldr(callable $callable, $accumulator = null, Foldable $foldable = null)
{
    return curryN(3, function (
        callable $callable,
        $accumulator,
        Foldable $foldable
    ) {
        return reduce(
            flip($callable),
            $accumulator,
            reduce(flip(prepend), fromNil(), $foldable)
        );
    })(...func_get_args());
}

/**
 * @var callable
 */
const filter = 'Widmogrod\Functional\filter';

/**
 * filter :: (a -> Bool) -> [a] -> [a]
 *
 * @param callable $predicate
 * @param Foldable $list
 *
 * @return Foldable|\Closure
 */
function filter(callable $predicate, Foldable $list = null)
{
    return curryN(2, function (callable $predicate, Foldable $list) {
        return reduce(function (Listt $list, $x) use ($predicate) {
            return $predicate($x)
                ? new ListtCons(function () use ($list, $x) {
                    return [$x, $list];
                }) : $list;
        }, fromNil(), $list);
    })(...func_get_args());
}

/**
 * @var callable
 */
const mpipeline = 'Widmogrod\Functional\mpipeline';

/**
 * mpipeline :: Monad m => (a -> m b) -> (b -> m c) -> m a -> m c
 *
 * @param callable $a
 * @param callable $b,...
 *
 * @return \Closure func($mValue) : mixed
 */
function mpipeline(callable $a, callable $b)
{
    return call_user_func_array(
        pipeline,
        array_map(bind, func_get_args())
    );
}

/**
 * @var callable
 */
const mcompose = 'Widmogrod\Functional\mcompose';

/**
 * compose :: Monad m => (b -> m c) -> (a -> m b) -> m a -> m c
 *
 * @param callable $a
 * @param callable $b,...
 *
 * @return \Closure func($mValue) : mixed
 */
function mcompose(callable $a, callable $b)
{
    return call_user_func_array(
        reverse(mpipeline),
        func_get_args()
    );
}

/**
 * tryCatch :: Exception e => (a -> b) -> (e -> b) -> a -> b
 *
 * @param callable $function
 * @param callable $catchFunction
 * @param $value
 *
 * @return mixed
 */
function tryCatch(callable $function, callable $catchFunction, $value)
{
    return curryN(3, function (callable $function, callable $catchFunction, $value) {
        try {
            return $function($value);
        } catch (\Exception $e) {
            return $catchFunction($e);
        }
    })(...func_get_args());
}

/**
 * @var callable
 */
const reThrow = 'Widmogrod\Functional\reThrow';

/**
 * reThrow :: Exception e => e -> a
 *
 * @param \Exception $e
 *
 * @throws \Exception
 */
function reThrow(\Exception $e)
{
    throw $e;
}

/**
 * @var callable
 */
const liftM2 = 'Widmogrod\Functional\liftM2';

/**
 * Lift result of transformation function, called with values from two monads.
 *
 * liftM2 :: Monad m => (a -> b -> c) -> m a -> m b -> m c
 *
 * Promote a function to a monad, scanning the monadic arguments from left to right. For example,
 *  liftM2 (+) [0,1] [0,2] = [0,2,1,3]
 *  liftM2 (+) (Just 1) Nothing = Nothing
 *
 * @param callable $transformation
 * @param Monad    $ma
 * @param Monad    $mb
 *
 * @return Monad|\Closure
 */
function liftM2(
    callable $transformation = null,
    Monad $ma = null,
    Monad $mb = null
) {
    return call_user_func_array(
        liftA2,
        func_get_args()
    );
}

/**
 * @var callable
 */
const bindM2 = 'Widmogrod\Functional\bindM2';

/**
 * bindM2 :: Monad m => (a -> b -> m c) -> m a -> m b -> m c
 *
 * @param callable $transformation
 * @param Monad    $ma
 * @param Monad    $mb
 *
 * @return Monad|\Closure
 */
function bindM2(
    callable $transformation = null,
    Monad $ma = null,
    Monad $mb = null
) {
    return curryN(
        3,
        function (
            callable $transformation,
            Monad $ma,
            Monad $mb
        ) {
            return $ma->bind(function ($a) use ($mb, $transformation) {
                return $mb->bind(function ($b) use ($a, $transformation) {
                    return $transformation($a, $b);
                });
            });
        }
    )(...func_get_args());
}

/**
 * @var callable
 */
const liftA2 = 'Widmogrod\Functional\liftA2';

/**
 * liftA2 :: Applicative f => (a -> b -> c) -> f a -> f b -> f c
 *
 * @param callable    $transformation
 * @param Applicative $fa
 * @param Applicative $fb
 *
 * @return Applicative|\Closure
 */
function liftA2(
    callable $transformation = null,
    Applicative $fa = null,
    Applicative $fb = null
) {
    return curryN(3, function (
        callable $transformation,
        Applicative $fa,
        Applicative $fb
    ) {
        return $fa->map(function ($a) use ($transformation) {
            return function ($b) use ($a, $transformation) {
                return $transformation($a, $b);
            };
        })->ap($fb);
    })(...func_get_args());
}

/**
 * @var callable
 */
const sequenceM = 'Widmogrod\Functional\sequenceM';

/**
 * sequenceM :: Monad m => m a -> m b -> m b
 *
 * a.k.a haskell >>
 *
 * Sequentially compose two actions, discarding any value produced by the first,
 * like sequencing operators (such as the semicolon) in imperative languages.
 *
 * This implementation allow to **compose more than just two monads**.
 *
 * @param Monad $a
 * @param Monad $b
 *
 * @return Monad|\Closure
 */
function sequenceM(Monad $a, Monad $b = null): Monad
{
    return curryN(2, function (Monad ...$monads): Monad {
        return array_reduce($monads, function (?Monad $a, Monad $b) {
            return $a
                ? $a->bind(function () use ($b) {
                    return $b;
                })
                : $b;
        }, null);
    })(...func_get_args());
}

/**
 * @var callable
 */
const traverse = 'Widmogrod\Functional\traverse';

/**
 * traverse :: Applicative f => (a -> f b) -> t a -> f (t b)
 *
 * Map each element of a structure to an action, evaluate these actions from left to right, and collect the results
 *
 * @param callable    $transformation (a -> f b)
 * @param Traversable $t              t a
 *
 * @return \Closure|Applicative f (t b)
 */
function traverse(callable $transformation, Traversable $t = null)
{
    return curryN(2, function (
        callable $transformation,
        Traversable $t
    ) {
        return $t->traverse($transformation);
    })(...func_get_args());
}

/**
 * filterM :: Monad m => (a -> m Bool) -> [a] -> m [a]
 *
 * ```haskell
 * filterM p = foldr (\ x -> liftA2 (\ flg -> if flg then (x:) else id) (p x)) (pure [])
 * foldr :: (a -> b -> b) -> b -> t a -> b
 * liftA2 :: (a -> b -> c) -> f a -> f b -> f c
 *```
 * @param callable $f  (a -> m Bool)
 * @param Foldable $xs [a]
 *
 * @return \Closure|Monad m [a]
 */
function filterM(callable $f, Foldable $xs = null)
{
    return curryN(2, function (
        callable $f,
        $xs
    ) {
        $result = foldr(function ($x, $ys) use ($f) {
            $y = $f($x);
            // Detect type of monad
            if ($ys === null) {
                $ys = $y::of(fromNil());
            }

            return liftA2(function (bool $flg, $ys) use ($x) {
                return $flg
                    ? prepend($x, $ys)
                    : $ys;
            }, $y, $ys);
        }, null, $xs);

        return $result === null
            ? fromNil()
            : $result;
    })(...func_get_args());
}

/**
 * foldM :: (Foldable t, Monad m) => (b -> a -> m b) -> b -> t a -> m b
 *
 * ```haskell
 * foldlM :: (Foldable t, Monad m) => (b -> a -> m b) -> b -> t a -> m b
 * foldlM f z0 xs = foldr f' return xs z0
 *      where f' x k z = f z x >>= k
 *
 * foldr :: (a -> b -> b) -> b -> t a -> b
 * ```
 *
 * @param callable $f  (a -> b -> m a)
 * @param null     $z0
 * @param Foldable $xs [b]
 *
 * @return mixed m a
 */
function foldM(callable $f, $z0 = null, Foldable $xs = null)
{
    return curryN(3, function (
        callable $f,
        $z0,
        $xs
    ) {
        $result = foldr(function ($x, $k) use ($f, $z0) {
            if ($k === null) {
                return $f($z0, $x);
            } else {
                return $k->bind(function ($z) use ($f, $x) {
                    return $f($z, $x);
                });
            }
        }, null, $xs);

        return $result === null
            ? fromNil()
            : $result;
    })(...func_get_args());
}
