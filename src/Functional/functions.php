<?php
namespace Functional;

use Monad;

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
        if ($argsLeft <= 0) {
            foreach (func_get_args() as $arg) {
                $args[] = $arg;
            }

            return call_user_func_array($function, $args);
        } else {
            array_push($args, func_num_args());

            return curryN($argsLeft, $function, $args);
        }
    };
}

/**
 * Lift result of monad bind to monad
 *
 * @param Monad\MonadInterface $monad
 * @param callable $transformation
 * @return Monad\MonadInterface
 */
function lift(Monad\MonadInterface $monad, callable $transformation)
{
    if ($monad instanceof Monad\Feature\LiftInterface) {
        return $monad->lift($transformation);
    }

    $result = $monad->bind($transformation);
    if ($result instanceof Monad\MonadInterface) {
        return $result;
    }

    return $monad::create($monad->bind($transformation));
}

/**
 * Apply two monads to
 *
 * @param Monad\MonadInterface $m1
 * @param Monad\MonadInterface $m2
 * @param callable $transformation
 * @return Monad\MonadInterface
 */
function liftM2(Monad\MonadInterface $m1, Monad\MonadInterface $m2, callable $transformation)
{
    return lift($m1, function ($a) use ($m2, $transformation) {
        return lift($m2, function ($b) use ($a, $transformation) {
            return call_user_func($transformation, $a, $b);
        });
    });
}
