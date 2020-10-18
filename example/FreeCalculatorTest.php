<?php

declare(strict_types=1);

namespace example;

use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\Identity;
use Widmogrod\Primitive\Stringg;
use Widmogrod\Useful\PatternMatcher;
use function Widmogrod\Functional\compose;
use function Widmogrod\Functional\bindM2;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Free\liftF;
use function Widmogrod\Useful\matchPatterns;

/**
 *  Exp a next
 *      = IntVal a (a -> next)
 *      | Sum a a (a -> next)
 *      | Multiply a a (a -> next)
 *      | Square a (a -> next)
 */
interface ExpF extends Functor, PatternMatcher
{
}

class IntVal implements ExpF
{
    private $int;
    private $next;

    public function __construct(int $int, callable $next)
    {
        $this->int = $int;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->int,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->int, $this->next);
    }
}

class Sum implements ExpF
{
    private $a;
    private $b;
    private $next;

    public function __construct($a, $b, callable $next)
    {
        $this->a = $a;
        $this->b = $b;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->a,
            $this->b,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->b, $this->next);
    }
}

class Multiply implements ExpF
{
    private $a;
    private $b;
    private $next;

    public function __construct($a, $b, callable $next)
    {
        $this->a = $a;
        $this->b = $b;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->a,
            $this->b,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->b, $this->next);
    }
}


class Square implements ExpF
{
    private $a;
    private $next;

    public function __construct($a, callable $next)
    {
        $this->a = $a;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->a,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->next);
    }
}
const sum = 'example\sum';

function sum(MonadFree $a, MonadFree $b): MonadFree
{
    return bindM2(function ($a, $b) {
        return liftF(new Sum($a, $b, Pure::of));
    }, $a, $b);
}

const int = 'example\int';

function int(int $int): MonadFree
{
    return liftF(new IntVal($int, Pure::of));
}

const mul = 'example\mul';

function mul(MonadFree $a, MonadFree $b): MonadFree
{
    return bindM2(function ($a, $b) {
        return liftF(new Multiply($a, $b, Pure::of));
    }, $a, $b);
}

const square = 'example\square';

function square(MonadFree $a): MonadFree
{
    return $a->bind(function ($a) {
        return liftF(new Square($a, Pure::of));
    });
}

const interpretInt = 'example\interpretInt';

/**
 * interpretInt :: ExpF -> Identity Free Int
 *
 * @return Identity
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function interpretInt(ExpF $f)
{
    return matchPatterns([
        IntVal::class => function (int $x, callable $next): Identity {
            return Identity::of($x)->map($next);
        },
        Sum::class => function (int $a, int $b, callable $next): Identity {
            return Identity::of($a + $b)->map($next);
        },
        Multiply::class => function (int $a, int $b, callable $next): Identity {
            return Identity::of($a * $b)->map($next);
        },
        Square::class => function (int $a, callable $next): Identity {
            return Identity::of(pow($a, 2))->map($next);
        },
    ], $f);
}

const interpretPrint = 'example\interpretPrint';

/**
 * interpretInt :: ExpF -> Identity Free Stringg
 *
 * @return Identity
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function interpretPrint(ExpF $f)
{
    return matchPatterns([
        IntVal::class => function (int $x, callable $next): Identity {
            return Identity::of(Stringg::of("$x"))->map($next);
        },
        Sum::class => function (Stringg $a, Stringg $b, callable $next): Identity {
            return Identity::of(
                Stringg::of('(')->concat($a->concat(Stringg::of('+'))->concat($b))->concat(Stringg::of(')'))
            )->map($next);
        },
        Multiply::class => function (Stringg $a, Stringg $b, callable $next): Identity {
            return Identity::of(
                Stringg::of('(')->concat($a->concat(Stringg::of('*'))->concat($b))->concat(Stringg::of(')'))
            )->map($next);
        },
        Square::class => function (Stringg $a, callable $next): Identity {
            return Identity::of(
                Stringg::of('(')->concat($a->concat(Stringg::of('^2')))->concat(Stringg::of(')'))
            )->map($next);
        },
    ], $f);
}

const optimizeCalc = 'example\optimizeCalc';

/**
 * optimizeCalc :: ExpF ->  ExpF
 *
 * @return Identity
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function optimizeCalc(ExpF $f)
{
    return matchPatterns([
        IntVal::class => function ($x, callable $next) {
            return new IntVal($x, $next);
        },
        Sum::class => function ($a, $b, callable $next) {
            return new Sum($a, $b, $next);
        },
        Multiply::class => function ($a, $b, callable $next) {
            return $a == $b
                ? new Square($a, $next)
                : new Multiply($a, $b, $next);
        },
        Square::class => function ($a, callable $next) {
            return new Square($a, $next);
        },
    ], $f);
}

class FreeCalculatorTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideCalculations
     */
    public function test_example_with_do_notation($calc, $expected)
    {
        $result = foldFree(interpretInt, $calc, Identity::of);
        $this->assertEquals(Identity::of($expected), $result);
    }

    public function provideCalculations()
    {
        return [
            '1' => [
                '$calc' => int(1),
                '$expected' => 1,
            ],
            '1 + 1' => [
                '$calc' => sum(
                    int(1),
                    int(1)
                ),
                '$expected' => 2,
            ],
            '2 * 3' => [
                '$calc' => mul(
                    int(2),
                    int(3)
                ),
                '$expected' => 6,
            ],
            '(1 + 1) * (2 * 3)' => [
                '$calc' => mul(
                    sum(int(1), int(1)),
                    mul(
                        int(2),
                        int(3)
                    )
                ),
                '$expected' => 12,
            ],
            '(2 * 3) ^ 2' => [
                '$calc' =>
                    square(
                        mul(
                            int(2),
                            int(3)
                        )
                    ),
                '$expected' => 36,
            ],
        ];
    }

    public function test_it_should_pretty_print()
    {
        $calc = mul(
            sum(int(1), int(1)),
            mul(
                int(2),
                square(int(3))
            )
        );

        $expected = '((1+1)*(2*(3^2)))';

        $result = foldFree(interpretPrint, $calc, Identity::of);
        $this->assertEquals(
            Identity::of(Stringg::of($expected)),
            $result
        );
    }

    public function test_it_should_optimize()
    {
        $calc = mul(
            sum(int(2), int(1)),
            sum(int(2), int(1))
        );

        $expected = '((2+1)^2)';

        $result = foldFree(compose(interpretPrint, optimizeCalc), $calc, Identity::of);
        $this->assertEquals(
            Identity::of(Stringg::of($expected)),
            $result
        );
    }
}
