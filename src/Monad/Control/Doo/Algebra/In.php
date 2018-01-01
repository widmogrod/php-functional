<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Algebra;

use Widmogrod\FantasyLand\Functor;

class In implements DooF
{
    private $names;
    private $fn;

    public function __construct(array $names, callable $fn)
    {
        $this->names = $names;
        $this->fn = $fn;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->names,
            $this->fn
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->names, $this->fn);
    }
}
