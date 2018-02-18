<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

/**
 * data List a = Nil | Cons a (List a)
 */
interface Listt extends
    FantasyLand\Monad,
    FantasyLand\Monoid,
    FantasyLand\Setoid,
    FantasyLand\Foldable,
    FantasyLand\Traversable,
    Common\ValueOfInterface
{
    /**
     * head :: [a] -> a
     *
     * @return mixed First element of Listt
     *
     * @throws EmptyListError
     */
    public function head();

    /**
     * tail :: [a] -> [a]
     *
     * @return \Widmogrod\Primitive\Listt
     *
     * @throws EmptyListError
     */
    public function tail(): self;
}
