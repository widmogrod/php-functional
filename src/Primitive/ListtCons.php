<?php

declare(strict_types=1);

namespace Widmogrod\Primitive;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;
use Widmogrod\Functional as f;

class ListtCons implements Listt, \IteratorAggregate
{
    public const of = 'Widmogrod\Primitive\ListtCons::of';

    /**
     * @var callable
     */
    private $next;

    /**
     * @inheritdoc
     */
    public static function of($value)
    {
        return new self(function () use ($value): array {
            return [$value, self::mempty()];
        });
    }

    public function __construct(callable $next)
    {
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $tail = $this;
        do {
            [$head, $tail] = $tail->headTail();
            yield $head;
        } while ($tail instanceof self);
    }

    /**
     * @inheritdoc
     */
    public function map(callable $transformation): FantasyLand\Functor
    {
        return new self(function () use ($transformation) {
            [$head, $tail] = $this->headTail();

            return [$transformation($head), $tail->map($transformation)];
        });
    }

    /**
     * @inheritdoc
     *
     * fs <*> xs = [f x | f <- fs, x <- xs]
     */
    public function ap(FantasyLand\Apply $applicative): FantasyLand\Apply
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
        foreach ($this as $item) {
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
    public function traverse(callable $fn)
    {
        return f\foldr(function ($x, $ys) use ($fn) {
            $functor = $fn($x);

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
        return new ListtNil();
    }

    /**
     * @inheritdoc
     *
     * @throws TypeMismatchError
     */
    public function concat(FantasyLand\Semigroup $value): FantasyLand\Semigroup
    {
        if ($value instanceof ListtNil) {
            return $this;
        }

        if ($value instanceof self) {
            return new self(function () use ($value) {
                [$x, $xs] = $this->headTail();

                return [$x, $xs->concat($value)];
            });
        }

        throw new TypeMismatchError($value, self::class);
    }

    /**
     * @inheritdoc
     */
    public function equals($other): bool
    {
        return $other instanceof self
            ? $this->extract() === $other->extract()
            : false;
    }

    /**
     * @inheritdoc
     */
    public function head()
    {
        [$head] = $this->headTail();

        return $head;
    }

    /**
     * @inheritdoc
     */
    public function tail(): Listt
    {
        [$head, $tail] = $this->headTail();

        return $tail;
    }

    public function headTail(): array
    {
        return call_user_func($this->next);
    }
}
