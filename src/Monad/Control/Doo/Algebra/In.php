<?php

declare(strict_types=1);

namespace Widmogrod\Monad\Control\Doo\Algebra;

use FunctionalPHP\FantasyLand\Functor;
use function Widmogrod\Functional\compose;

class In implements DooF
{
    private $names;
    private $fn;
    private $next;

    public function __construct(array $names, callable $fn, callable $next)
    {
        $this->names = $names;
        $this->fn = $fn;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->names,
            $this->fn,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->names, $this->fn, $this->next);
    }
}
