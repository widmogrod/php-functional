<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Algebra;

use Widmogrod\FantasyLand\Functor;
use Widmogrod\Useful\PatternMatcher;

/**
 *  DooF next = Let name m next
 *            | In [name] fn
 */
interface DooF extends Functor, PatternMatcher
{
}
