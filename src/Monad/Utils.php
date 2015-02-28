<?php
namespace Monad;

final class Utils
{
    /**
     * Reduce list of monads to single monad
     *
     * @param MonadInterface[] $listOfMonads
     * @param callable $reduce
     * @param mixed $base
     * @return MonadInterface
     */
    public function reduce($listOfMonads, callable $reduce, $base)
    {
        return array_reduce(
            $listOfMonads,
            function (LiftInterface $base, MonadInterface $monad) use ($reduce) {
                return $monad->bind(function ($value) use ($reduce, $base) {
                    return $base->lift(function ($base) use ($reduce, $value) {
                        return $reduce($base, $value);
                    });
                });
            },
            Unit::create($base)
        );
    }

    /**
     * Aggregate values from list of Monads into single monad with list of values.
     *
     * @param MonadInterface[] $listOfMonads
     * @return MonadInterface
     */
    public static function aggregate(array $listOfMonads)
    {
        return self::reduce(
            $listOfMonads,
            function ($base, $value) {
                $base[] = $value;
                return $base;
            },
            []
        );
    }

    /**
     * Apply values from monad to $transformation function and return result of this function
     *
     * @param MonadInterface $arguments
     * @param callable $transformation
     * @return mixed
     */
    public static function applyBind(MonadInterface $arguments, callable $transformation)
    {
        return $arguments->bind(function (array $arguments) use ($transformation) {
            return call_user_func_array($transformation, $arguments);
        });
    }

    /**
     * Apply values from monad to $transformation function and return result of this function
     *
     * @param LiftInterface $arguments
     * @param callable $transformation
     * @return LiftInterface
     */
    public static function applyLift(LiftInterface $arguments, callable $transformation)
    {
        return $arguments->lift(function (array $arguments) use ($transformation) {
            return call_user_func_array($transformation, $arguments);
        });
    }
}
