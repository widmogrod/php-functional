<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo;

use FunctionalPHP\FantasyLand\Monad;
use Widmogrod\Monad\Control\Doo\Algebra\DooF;
use Widmogrod\Monad\Control\Doo\Algebra\In;
use Widmogrod\Monad\Control\Doo\Algebra\Let;
use Widmogrod\Monad\Control\Doo\Registry\Registry;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\Reader;
use const Widmogrod\Monad\Reader\pure;
use function Widmogrod\Functional\sequenceM;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Reader\runReader;
use function Widmogrod\Useful\matchPatterns;

/**
 * @var callable
 */
const interpretation = 'Widmogrod\Monad\Control\Doo\interpretation';

/**
 * interpretationOfDoo :: DooF f -> Reader Registry MonadFree
 *
 * @param  DooF   $f
 * @return Reader
 *
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function interpretation(DooF $f)
{
    return matchPatterns([
        Let::class => function (string $name, Monad $m, MonadFree $next): Reader {
            return Reader::of(function (Registry $registry) use ($name, $m, $next) {
                return $m->bind(function ($v) use ($name, $next, $registry) {
                    $registry->set($name, $v);

                    return $next;
                });
            });
        },
        In::class => function (array $names, callable $fn, callable $next): Reader {
            return Reader::of(function (Registry $registry) use ($names, $fn, $next) {
                $args = array_map([$registry, 'get'], $names);

                return $next($fn(...$args));
            });
        },
    ], $f);
}

function doo(MonadFree ...$operation)
{
    $registry = new Registry();

    return runReader(
        foldFree(interpretation, sequenceM(...$operation), pure),
        $registry
    );
}
