<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Control\Doo;

use FunctionalPHP\FantasyLand\Monad;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use function Widmogrod\Monad\Free\liftF;

function let(string $name, Monad $m): MonadFree
{
    return $m instanceof MonadFree
        ? $m->bind(function (Monad $m) use ($name): MonadFree {
            return liftF(new Algebra\Let($name, $m, Pure::of(null)));
        })
        : liftF(new Algebra\Let($name, $m, Pure::of(null)));
}

function in(array $names, callable $fn): MonadFree
{
    return liftF(new Algebra\In($names, $fn, Pure::of));
}
