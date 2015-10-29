<?php
namespace Monad;

use Common;
use Functional as f;
use FantasyLand\Apply;
use FantasyLand\Monad;
use FantasyLand\Foldable;

class IO implements
    Monad,
    Foldable
{
    const of = 'Monad\IO::of';

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
    public function ap(Apply $b)
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
            $m = call_user_func($function, $this->run());
            return $m instanceof IO
                ? $m->run()
                : $m;
        });
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

    /**
     * @inheritdoc
     */
    public function reduce(callable $function, $accumulator)
    {
        return static::of(function () use ($function, $accumulator) {
            return call_user_func($function, $accumulator, $this->run());
        });
    }
}
