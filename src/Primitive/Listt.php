<?php

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use Widmogrod\FantasyLand;
use Widmogrod\Functional as f;

class Listt implements
    FantasyLand\Monad,
    FantasyLand\Monoid,
    FantasyLand\Setoid,
    FantasyLand\Foldable,
    FantasyLand\Traversable,
    Common\ValueOfInterface
{
    use Common\PointedTrait;

    public const of = 'Widmogrod\Primitive\Listt::of';

    /**
     * @param array $value
     */
    public function __construct($value)
    {
        $givenType = is_object($value) ? get_class($value) : gettype($value);
        assert(is_iterable($value), "Not iterable value given $givenType");

        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation)
    {
        $result = [];
        foreach ($this->value as $value) {
            $result[] = $transformation($value);
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
        return $this->reduce(function ($accumulator, $value) use ($applicative) {
            /** @var $applicative self */
            return f\concatM($accumulator, $applicative->map($value));
        }, self::mempty());
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $transformation)
    {
        // xs >>= f = concat (map f xs)
        return f\concat(f\map($transformation, $this));
    }

    /**
     * @inheritdoc
     */
    public function extract()
    {
        return $this->reduce(function ($accumulator, $value) {
            $accumulator[] = $value instanceof Common\ValueOfInterface
                ? $value->extract()
                : $value;

            return $accumulator;
        }, []);
    }

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        foreach ($this->value as $item) {
            $accumulator = $function($accumulator, $item);
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
     *
     * (<$>) :: Functor f => (a -> b) -> f a -> f b
     * (<*>) :: f (a -> b) -> f a -> f b
     */
    public function traverse(callable $f)
    {
        return f\foldr(function ($x, $ys) use ($f) {
            $functor = $f($x);

            return $functor
                ->map(f\prepend)
                ->ap($ys ?: $functor::of(self::mempty())); // https://github.com/widmogrod/php-functional/issues/30
        }, null, $this);
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return self::of([]);
    }

    /**
     * @inheritdoc
     */
    public function getEmpty()
    {
        return self::mempty();
    }

    /**
     * @inheritdoc
     *
     * @throws TypeMismatchError
     */
    public function concat(FantasyLand\Semigroup $value)
    {
        if ($value instanceof self) {
            return self::of($value->reduce(function ($accumulator, $item) {
                $accumulator[] = $item;

                return $accumulator;
            }, $this->value));
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @inheritdoc
     */
    public function equals($other)
    {
        return $other instanceof self
            ? $this->extract() === $other->extract()
            : false;
    }

    /**
     * head :: [a] -> a
     *
     * @return mixed First element of Listt
     *
     * @throws \BadMethodCallException
     */
    public function head()
    {
        return $this->guardEmptyGenerator('head of empty Listt')->current();
    }

    /**
     * tail :: [a] -> [a]
     *
     * @return \Widmogrod\Primitive\Listt
     *
     * @throws \BadMethodCallException
     */
    public function tail(): self
    {
        ($generator = $this->guardEmptyGenerator('tail of empty Listt'))->next();

        return $generator->valid()
            ? self::of((function ($values) {
                yield from $values;
            })($generator))
            : self::mempty();
    }

    private function guardEmptyGenerator(string $message): \Generator
    {
        /** @var \Generator $generator */
        $generator = (function ($values) {
            yield from $values;
        })($this->value);

        if (!$generator->valid()) {
            throw new \BadMethodCallException($message);
        }

        return $generator;
    }
}
