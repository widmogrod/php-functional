<?php
namespace Monad;

final class Utils
{
    /**
     * Aggregate values from list of Monads into single monad with list of values.
     *
     * @param MonadInterface[] $listOfMonads
     * @return MonadInterface
     */
    public static function aggregate(array $listOfMonads)
    {
        return array_reduce($listOfMonads, function (LiftInterface $carry, LiftInterface $monad) {
            return $monad->lift(function ($x) use ($carry) {
                return $carry->lift(function ($list) use ($x) {
                    $list[] = $x;
                    return $list;
                });
            });
        }, Unit::create([]));
    }

    /**
     * Apply values from monad to $transformation function and return result of this function
     *
     * @param BindInterface $arguments
     * @param callable $transformation
     * @return mixed
     */
    public static function applyBind(BindInterface $arguments, callable $transformation)
    {
        return $arguments->bind(function(array $arguments) use ($transformation) {
            return call_user_func_array($transformation, $arguments);
        });
    }

    /**
     * Apply values from monad to $transformation function and return result of this function
     *
     * @param LiftInterface $arguments
     * @param callable $transformation
     * @return MonadInterface
     */
    public static function applyLift(LiftInterface $arguments, callable $transformation)
    {
        return $arguments->lift(function(array $arguments) use ($transformation) {
            return call_user_func_array($transformation, $arguments);
        });
    }
}