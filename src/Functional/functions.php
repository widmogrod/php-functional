<?php
namespace Widmogrod\Functional;

use Widmogrod\Common\ValueOfInterface;
use Widmogrod\FantasyLand\Applicative;
use Widmogrod\FantasyLand\Foldable;
use Widmogrod\FantasyLand\Functor;
use Widmogrod\FantasyLand\Monad;
use Widmogrod\FantasyLand\Traversable;
use Widmogrod\Monad\Collection;
use Widmogrod\Monad\Identity;

/**
 * @var callable
 */
const push = 'Widmogrod\Functional\push';

/**
 * push :: [a] -> [a] -> [a]
 *
 * Append array with values.
 *
 * @param array $array
 * @param array $values
 * @return array
 */
function push(array $array, array $values)
{
    foreach ($values as $value) {
        $array[] = $value;
    }

    return $array;
}

/**
 * @var callable
 */
const append = 'Widmogrod\Functional\append';

/**
 * append :: [a] -> a -> [a]
 *
 * @param array $list
 * @param mixed $value
 * @return array
 */
function append($list, $value = null)
{
    return call_user_func_array(curryN(2, function ($list, $value) {
        return push(
            toNativeTraversable($list),
            toNativeTraversable($value)
        );
    }), func_get_args());
}

/**
 * @var callable
 */
const applicator = 'Widmogrod\Functional\applicator';

/**
 * applicator :: a -> (a -> b) -> b
 *
 * @param mixed $x
 * @param callable $f
 * @return mixed
 */
function applicator($x, callable $f = null)
{
    return call_user_func_array(curryN(2, function ($y, callable $f) {
        return call_user_func($f, $y);
    }), func_get_args());
}

/**
 * @var callable
 */
const invoke = 'Widmogrod\Functional\invoke';

/**
 * invoke :: a -> #{a: (_ -> b)} -> b
 *
 * @param string $method
 * @param mixed $object
 * @return mixed
 */
function invoke($method, $object = null)
{
    return call_user_func_array(curryN(2, function ($method, $object) {
        return call_user_func([$object, $method]);
    }), func_get_args());
}

/**
 * @var callable
 */
const toFoldable = 'Widmogrod\Functional\toFoldable';

/**
 * toFoldable :: Foldable t => a -> t a
 *
 * @param Foldable|\Traversable|array|mixed $value
 * @return Foldable
 */
function toFoldable($value)
{
    return $value instanceof Foldable
        ? $value
        : Collection::of(toNativeTraversable($value));
}

/**
 * @var callable
 */
const toTraversable = 'Widmogrod\Functional\toTraversable';

/**
 * toTraversable :: Traversable t => a -> t a
 *
 * @param Traversable|\Traversable|array|mixed $value
 * @return Traversable
 */
function toTraversable($value)
{
    return $value instanceof Traversable
        ? $value
        : Collection::of(toNativeTraversable($value));
}

/**
 * @var callable
 */
const concat = 'Widmogrod\Functional\concat';

/**
 * concat :: Foldable t => t [a] -> [a]
 *
 * <code>
 * concat([[1, 2], [3, 4]]) == [1, 2, 3, 4]
 * </code>
 *
 * The concatenation of all the elements of a container of lists.
 *
 * @param Foldable $foldable
 * @return array
 */
function concat(Foldable $foldable)
{
    return reduce(function ($agg, $value) {
        return reduce(function ($agg, $v) {
            $agg[] = $v;

            return $agg;
        }, $agg, toFoldable($value));
    }, [], $foldable);
}

/**
 * @var callable
 */
const toList = 'Widmogrod\Functional\toList';

/**
 * toList :: Traversable t -> t a -> [a]
 *
 * @param Foldable $traversable
 * @return mixed
 */
function toList(Foldable $traversable)
{
    return reduce(append, [], $traversable);
}

/**
 * @var callable
 */
const identity = 'Widmogrod\Functional\identity';

/**
 * Return value passed to function
 *
 * @param mixed $x
 * @return mixed
 */
function identity($x)
{
    return $x;
}

/**
 * Curry function
 *
 * @param integer $numberOfArguments
 * @param callable $function
 * @param array $args
 * @return callable
 */
function curryN($numberOfArguments, callable $function, array $args = [])
{
    return function () use ($numberOfArguments, $function, $args) {
        $argsLeft = $numberOfArguments - func_num_args();

        return $argsLeft <= 0
            ? call_user_func_array($function, push($args, func_get_args()))
            : curryN($argsLeft, $function, push($args, func_get_args()));
    };
}

/**
 * Curry function
 *
 * @param callable $function
 * @param array $args
 * @return callable
 */
function curry(callable $function, array $args = [])
{
    $reflectionOfFunction = new \ReflectionFunction($function);

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
 * @param mixed $value
 * @return \Closure
 */
function tee(callable $function = null, $value = null)
{
    return call_user_func_array(curryN(2, function (callable $function, $value) {
        call_user_func($function, $value);

        return $value;
    }), func_get_args());
}

/**
 * @var callable
 */
const compose = 'Widmogrod\Functional\compose';

/**
 * Compose multiple functions into one.
 * Composition starts from right to left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'abc'
 * strtolower(strtouppser('aBc'))  ≡ 'abc'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($value) : mixed
 */
function compose(callable $a, callable $b)
{
    return call_user_func_array(
        reverse('Widmogrod\Functional\pipeline'),
        func_get_args()
    );
}

/**
 * @var callable
 */
const pipeline = 'Widmogrod\Functional\pipeline';

/**
 * Compose multiple functions into one.
 * Composition starts from left.
 *
 * <code>
 * compose('strtolower', 'strtoupper')('aBc') ≡ 'ABC'
 * strtouppser(strtolower('aBc'))  ≡ 'ABC'
 * </code>
 *
 * @param callable $a
 * @param callable $b,...
 * @return \Closure         func($value) : mixed
 */
function pipeline(callable $a, callable $b)
{
    $list = func_get_args();

    return function ($value = null) use (&$list) {
        return array_reduce($list, function ($accumulator, callable $a) {
            return call_user_func($a, $accumulator);
        }, $value);
    };
}

/**
 * @var callable
 */
const reverse = 'Widmogrod\Functional\reverse';

/**
 * Call $function with arguments in reversed order
 *
 * @return \Closure
 * @param callable $function
 */
function reverse(callable $function)
{
    return function () use ($function) {
        return call_user_func_array($function, array_reverse(func_get_args()));
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
 * @param callable $transformation
 * @param Functor $value
 */
function map(callable $transformation = null, Functor $value = null)
{
    return call_user_func_array(curryN(2, function (callable $transformation, Functor $value) {
        return $value->map($transformation);
    }), func_get_args());
}

/**
 * @var callable
 */
const bind = 'Widmogrod\Functional\bind';

/**
 * bind :: Monad m => (a -> m b) -> m a -> m b
 *
 * @return mixed|\Closure
 * @param callable $function
 * @param Monad $value
 */
function bind(callable $function = null, Monad $value = null)
{
    return call_user_func_array(curryN(2, function (callable $function, Monad $value) {
        return $value->bind($function);
    }), func_get_args());
}

/**
 * @var callable
 */
const join = 'Widmogrod\Functional\join';

/**
 * join :: Monad (Monad m) -> Monad m
 *
 * @param Monad $monad
 * @return Monad
 */
function join(Monad $monad = null)
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
 * @param callable $callable Binary function ($accumulator, $value)
 * @param mixed $accumulator
 * @param Foldable $foldable
 * @return mixed
 */
function reduce(callable $callable, $accumulator = null, Foldable $foldable = null)
{
    return call_user_func_array(curryN(3, function (
        callable $callable,
        $accumulator,
        Foldable $foldable
    ) {
        return $foldable->reduce($callable, $accumulator);
    }), func_get_args());
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
 * @param callable $callable Binary function ($value, $accumulator)
 * @param mixed $accumulator
 * @param Foldable $foldable
 * @return mixed
 */
function foldr(callable $callable, $accumulator = null, Foldable $foldable = null)
{
    return call_user_func_array(curryN(3, function (
        callable $callable,
        $accumulator,
        Foldable $foldable
    ) {
        return reduce(
            $callable,
            $accumulator,
            toFoldable(reduce(flip(append), [], $foldable))
        );
    }), func_get_args());
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
 * @return Foldable
 */
function filter(callable $predicate, Foldable $list = null)
{
    return call_user_func_array(curryN(2, function (callable $predicate, Foldable $list) {
        return reduce(function ($list, $x) use ($predicate) {
            return call_user_func($predicate, $x)
                ? append($list, $x)
                : $list;
        }, [], $list);
    }), func_get_args());
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
 * @return \Closure         func($mValue) : mixed
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
 * @return \Closure         func($mValue) : mixed
 */
function mcompose(callable $a, callable $b)
{
    return call_user_func_array(
        reverse(mpipeline),
        func_get_args()
    );
}

/**
 * @var callable
 */
const flip = 'Widmogrod\Functional\flip';

/**
 * flip :: (a -> b -> c) -> (b -> a -> c)
 *
 * @param callable $func
 * @return callable
 */
function flip(callable $func)
{
    $args = func_get_args();
    array_shift($args);

    return call_user_func_array(curryN(2, function ($a, $b) use ($func) {
        $args = func_get_args();
        $args[0] = $b;
        $args[1] = $a;

        return call_user_func_array($func, $args);
    }), $args);
}

/**
 * @var callable
 */
const isNativeTraversable = 'Widmogrod\Functional\isNativeTraversable';

/**
 * isNativeTraversable :: a -> Boolean
 *
 * Evaluate if value is a traversable
 *
 * @param mixed $value
 * @return bool
 */
function isNativeTraversable($value)
{
    return is_array($value) || $value instanceof \Traversable;
}

/**
 * @var callable
 */
const toNativeTraversable = 'Widmogrod\Functional\toNativeTraversable';

/**
 * toNativeTraversable :: a -> [a]
 *
 * @param mixed $value
 * @return array
 */
function toNativeTraversable($value)
{
    return isNativeTraversable($value) ? $value : [$value];
}

/**
 * @var callable
 */
const head = 'Widmogrod\Functional\head';

/**
 * Return head of a traversable
 *
 * @param array|\Traversable $list
 * @return null|array|\Traversable
 */
function head($list)
{
    if (!isNativeTraversable($list)) {
        return null;
    }
    foreach ($list as $item) {
        return $item;
    }

    return null;
}

/**
 * @var callable
 */
const tail = 'Widmogrod\Functional\tail';

/**
 * Return tail of a traversable
 *
 * TODO support \Traversable
 *
 * @param array $list
 * @return null
 */
function tail(array $list)
{
    if (count($list) === 0) {
        return null;
    }

    $clone = $list;
    array_shift($clone);

    return $clone;
}

/**
 * tryCatch :: Exception e => (a -> b) -> (e -> b) -> a -> b
 *
 * @param callable $function
 * @param callable $catchFunction
 * @param $value
 * @return mixed
 */
function tryCatch(callable $function, callable $catchFunction, $value)
{
    return call_user_func_array(curryN(3, function (callable $function, callable $catchFunction, $value) {
        try {
            return call_user_func($function, $value);
        } catch (\Exception $e) {
            return call_user_func($catchFunction, $e);
        }
    }), func_get_args());
}

/**
 * @var callable
 */
const reThrow = 'Widmogrod\Functional\reThrow';

/**
 * reThrow :: Exception e => e -> a
 *
 * @param \Exception $e
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
 * Lift result of transformation function , called with values from two monads.
 *
 * liftM2 :: Monad m => (a -> b -> c) -> m a -> m b -> m c
 *
 * Promote a function to a monad, scanning the monadic arguments from left to right. For example,
 *  liftM2 (+) [0,1] [0,2] = [0,2,1,3]
 *  liftM2 (+) (Just 1) Nothing = Nothing
 *
 * @param callable $transformation
 * @param Monad $ma
 * @param Monad $mb
 * @return Monad|\Closure
 */
function liftM2(
    callable $transformation = null,
    Monad $ma = null,
    Monad $mb = null
) {
    return call_user_func_array(curryN(3,
        function (
            callable $transformation,
            Monad $ma,
            Monad $mb
        ) {
            return $ma->bind(function ($a) use ($mb, $transformation) {
                return $mb->bind(function ($b) use ($a, $transformation) {
                    return call_user_func($transformation, $a, $b);
                });
            });
        }), func_get_args());
}

/**
 * @var callable
 */
const liftA2 = 'Widmogrod\Functional\liftA2';

/**
 * liftA2 :: Applicative f => (a -> b -> c) -> f a -> f b -> f c
 *
 * @param callable $transformation
 * @param Applicative $fa
 * @param Applicative $fb
 * @return Applicative|\Closure
 */
function liftA2(
    callable $transformation = null,
    Applicative $fa = null,
    Applicative $fb = null
) {
    return call_user_func_array(curryN(3, function (
        callable $transformation,
        Applicative $fa,
        Applicative $fb
    ) {
        return $fa->map(function ($a) use ($transformation) {
            return function ($b) use ($a, $transformation) {
                return call_user_func($transformation, $a, $b);
            };
        })->ap($fb);
    }), func_get_args());
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
 * Sequentially compose two actions, discarding any value produced by the first, like sequencing operators (such as the semicolon) in imperative languages.
 *
 * @param Monad $a
 * @param Monad $b
 * @return Monad
 */
function sequenceM(Monad $a, Monad $b)
{
    return $a->bind(function () use ($b) {
        return $b;
    });
}

/**
 * @var callable
 */
const sequence_ = 'Widmogrod\Functional\sequence_';

/**
 * sequence_ :: Monad m => [m a] -> m ()
 *
 * @todo consider to do it like this: foldr (>>) (return ())
 *
 * @param Monad[] $monads
 * @return Monad
 */
function sequence_($monads)
{
    return reduce(sequenceM, Identity::of([]), toFoldable($monads));
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
 * @param callable $transformation  (a -> f b)
 * @param Traversable      $t t a
 * @return Applicative     f (t b)
 */
function traverse(callable $transformation, Traversable $t = null)
{
    return call_user_func_array(curryN(2, function (
        callable $transformation,
        Traversable $t
    ) {
        return $t->traverse($transformation);
    }), func_get_args());
}

/**
 * @var callable
 */
const sequence = 'Widmogrod\Functional\sequence';

/**
 * sequence :: Monad m => t (m a) -> m (t a)
 *
 * @param Traversable|Monad[] $monads
 * @return Monad
 */
function sequence($monads)
{
    return traverse(identity, toTraversable($monads));
}
