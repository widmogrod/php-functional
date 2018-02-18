<?php

declare(strict_types=1);
namespace Widmogrod\Monad\Control\Doo\Algebra;

use FunctionalPHP\FantasyLand\Functor;
use FunctionalPHP\FantasyLand\Monad;
use Widmogrod\Monad\Free\MonadFree;

class Let implements DooF
{
    private $name;
    private $m;
    private $next;

    public function __construct(string $name, Monad $m, MonadFree $next)
    {
        $this->name = $name;
        $this->m = $m;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->name,
            $this->m,
            $function($this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->name, $this->m, $this->next);
    }
}
