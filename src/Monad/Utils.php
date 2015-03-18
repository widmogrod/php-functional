<?php
namespace Monad;

use Functional as f;

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
                    return f\lift($base, function ($base) use ($reduce, $value) {
                        return $reduce($base, $value);
                    });
                });
            },
            Identity::create($base)
        );
    }
}
