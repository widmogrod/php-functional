<?php
namespace Monad;

use Common;
use Exception;
use FantasyLand;
use Functional as f;

class Collection implements
    FantasyLand\Monad,
    FantasyLand\Foldable,
    FantasyLand\Traversable,
    Common\ValueOfInterface
{
    use Common\PointedTrait;

    const of = 'Monad\Collection::of';

    /**
     * @param array $value
     */
    public function __construct($value)
    {
        $this->value = f\isNativeTraversable($value)
            ? $value
            : [$value];
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        $result = [];
        foreach ($this->value as $key => $value) {
            $result[$key] = call_user_func($transformation, $value);
        }

        return self::of($result);
    }

    /**
     * @inheritdoc
     *
     * fs <*> xs = [f x | f <- fs, x <- xs]
     */
    public function ap(FantasyLand\Apply $applicative)
    {
        // Sine in php List comprehension is not available, then I doing it like this
        $result = [];
        $isCollection = $applicative instanceof Collection;

        foreach ($this->extract() as $value) {
            $partial = $applicative->map($value)->extract();
            if ($isCollection) {
                $result = f\push($result, $partial);
            } else {
                $result[] = $partial;
            }
        }

        return $applicative::of($result);
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        // xs >>= f = concat (map f xs)
        return self::of(f\concat(f\map($transformation, $this)));
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return array_map(function ($value) {
            return $value instanceof Common\ValueOfInterface
                ? $value->extract()
                : $value;
        }, $this->value);
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        foreach ($this->value as $item) {
            $accumulator = call_user_func($function, $accumulator, $item);
        }

        return $accumulator;
    }

    /**
     * @inheritdoc
     *
     * Example from haskell source code:
     * ``` haskell
     * traverse f = List.foldr cons_f (pure [])
     *  where cons_f x ys = (:) <$> f x <*> ys
     * ```
     */
    public function traverse(callable $transformation)
    {
        return f\foldr(function ($ys, $x) use ($transformation) {
            return call_user_func($transformation, $x)->map(f\append)->ap($ys);
        }, self::of([[]]), $this);
    }
}
