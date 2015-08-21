<?php
namespace Monad;

use Common;
use Exception;
use FantasyLand;
use Functional as f;

class Collection implements
    FantasyLand\MonadInterface,
    Common\ValueOfInterface,
    Common\ConcatInterface
{
    use Common\PointedTrait;

    const of = 'Monad\Collection::of';

    /**
     * @param array $value
     */
    public function __construct($value)
    {
        $this->value = is_array($value) || $value instanceof \Traversable
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
     */
    public function ap(FantasyLand\ApplyInterface $applicative)
    {
        // Sine in php List comprehension is not available, then I doing it like this
        $result = [];
        $isCollection = $applicative instanceof Collection;

        foreach ($this->extract() as $value) {
            $partial = $applicative->map($value)->extract();
            if ($isCollection) {
                $result = \Functional\push($result, $partial);
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
        $result = [];
        foreach ($this->value as $index => $value) {
            $result = f\concat(
                $result,
                $value instanceof FantasyLand\MonadInterface
                    ? $value->bind($transformation)
                    : call_user_func($transformation, $value, $index)
            );
        }

        return static::of($result);
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
    public function concat($value)
    {
        if ($value instanceof self) {
            return $value->concat($this->value);
        }

        return f\concat($value, $this->value);
    }
}
