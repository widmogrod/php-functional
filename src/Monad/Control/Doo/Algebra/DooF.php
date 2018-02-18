<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Algebra;

use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Useful\PatternMatcher;

/**
 *  DooF next = Let name m next
 *            | In [name] fn (m -> next)
 */
interface DooF extends Functor, PatternMatcher
{
}
