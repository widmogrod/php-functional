<?php
namespace Monad;

final class Utils
{
    const returns = 'Monad\Utils::returns';

    /**
     * Return passed value.
     *
     * @param mixed $value
     * @return mixed
     */
    public static function returns($value)
    {
        return $value;
    }

    /**
     * Lift result of monad bind to monad
     *
     * @param MonadInterface $monad
     * @param callable $transformation
     * @return MonadInterface
     */
    public static function lift(MonadInterface $monad, callable $transformation)
    {
        if ($monad instanceof Feature\LiftInterface) {
            return $monad->lift($transformation);
        }

        $result = $monad->bind($transformation);
        if ($result instanceof MonadInterface) {
            return $result;
        }

        return $monad::create($monad->bind($transformation));
    }

    /**
     * Apply two monads to
     *
     * @param MonadInterface $m1
     * @param MonadInterface $m2
     * @param callable $transformation
     * @return MonadInterface
     */
    public static function liftM2(MonadInterface $m1, MonadInterface $m2, callable $transformation)
    {
        return self::lift($m1, function ($a) use ($m2, $transformation) {
            return self::lift($m2, function ($b) use ($a, $transformation) {
                return call_user_func($transformation, $a, $b);
            });
        });
    }

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
            function (MonadInterface $base, MonadInterface $monad) use ($reduce) {
                return $monad->bind(function ($value) use ($reduce, $base) {
                    return self::lift($base, function ($base) use ($reduce, $value) {
                        return $reduce($base, $value);
                    });
                });
            },
            Unit::create($base)
        );
    }
}
