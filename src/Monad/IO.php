<?php
namespace Monad;

use Functional as f;
use FantasyLand\ApplyInterface;
use FantasyLand\MonadInterface;

class IO implements MonadInterface
{
    const of = 'Monad\IO::of';

    /**
     * @var callable
     */
    private $unsafe;

    public function __construct(callable $unsafe)
    {
        $this->unsafe = $unsafe;
    }

    /**
     * @inheritdoc
     */
    public static function of($value)
    {
        return new static($value);
    }

    /**
     * @inheritdoc
     */
    public function ap(ApplyInterface $b)
    {
        return $b->map($this->run());
    }

    /**
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        return call_user_func($function, $this->run());
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function)
    {
        return $this->bind(function ($value) use ($function) {
            return static::of(function () use ($function, $value) {
                return call_user_func($function, $value);
            });
        });
    }

    /**
     * Perform unsafe operation
     *
     * @return mixed
     */
    public function run()
    {
        return call_user_func($this->unsafe);
    }
}
