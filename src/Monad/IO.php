<?php

declare(strict_types=1);

namespace Widmogrod\Monad;

use Widmogrod\Common;
use FunctionalPHP\FantasyLand;

class IO implements
    FantasyLand\Monad,
    FantasyLand\Foldable
{
    const of = 'Widmogrod\Monad\IO::of';

    use Common\PointedTrait;

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
    public function ap(FantasyLand\Apply $b): FantasyLand\Apply
    {
        return $b->map($this->run());
    }

    /**
     * bind :: IO a -> (a -> IO b) -> IO b
     *
     * @inheritdoc
     */
    public function bind(callable $function)
    {
        // Theoretical here should be call like this:
        //  call_user_func($function, $this->run())
        // But this do not make things lazy, to cheat little bit
        // IO monad is returned and inside of it is little switch
        return static::of(function () use ($function) {
            $m = $function($this->run());

            return $m instanceof IO
                ? $m->run()
                : $m;
        });
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): FantasyLand\Functor
    {
        return $this->bind(function ($value) use ($function) {
            return static::of(function () use ($function, $value) {
                return $function($value);
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

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        return static::of(function () use ($function, $accumulator) {
            return $function($accumulator, $this->run());
        });
    }
}
