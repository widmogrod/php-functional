<?php

declare(strict_types=1);

namespace example;

use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\State;
use const Widmogrod\Monad\State\value;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\push_;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Free\liftF;
use function Widmogrod\Useful\matchPatterns;

interface ScenarioF extends Functor
{
}

class Given implements ScenarioF
{
    public $desc;
    public $state;
    public $next;

    public function __construct(string $desc, $state, MonadFree $next)
    {
        $this->desc = $desc;
        $this->state = $state;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->desc,
            $this->state,
            $function($this->next)
        );
    }
}

class When implements ScenarioF
{
    public $action;
    public $next;

    public function __construct(string $action, MonadFree $next)
    {
        $this->action = $action;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->action,
            $function($this->next)
        );
    }
}

class Then implements ScenarioF
{
    public $assertion;
    public $next;

    public function __construct(string $assertion, MonadFree $next)
    {
        $this->assertion = $assertion;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->assertion,
            $function($this->next)
        );
    }
}

function scenario(string $desc, $state): Scenario
{
    return new Scenario(given_($desc, $state));
}

function given_(string $desc, $state): MonadFree
{
    return liftF(new Given($desc, $state, Pure::of('-given-')));
}

function when_(string $action): MonadFree
{
    return liftF(new When($action, Pure::of('-when-')));
}

function then_(string $assertion): MonadFree
{
    return liftF(new Then($assertion, Pure::of('-then-')));
}


class Scenario
{
    private $free;

    public function __construct(MonadFree $free)
    {
        $this->free = $free;
    }

    public function When(string $action): self
    {
        return new self($this->free->bind(function () use ($action) {
            return when_($action);
        }));
    }

    public function Then(string $assertion): self
    {
        return new self($this->free->bind(function () use ($assertion) {
            return then_($assertion);
        }));
    }

    public function Run(array $when, array $then)
    {
        $interpretAction = curryN(3, interpretAction);
        $interpretAssertion = curryN(3, interpretAssertion);
        $interpretScenario = curryN(3, interpretScenario);

        $interpret = $interpretScenario($interpretAction($when), $interpretAssertion($then));

        $state = foldFree($interpret, $this->free, value);
        $result = State\execState($state, []);

        return $result;
    }
}

function Given(string $desc, $state): Scenario
{
    return new Scenario(given_($desc, $state));
}

const interpretScenario = 'example\interpretScenario';

/**
 * interpretScenario :: (a -> b) -> (a -> Bool) -> ScenarioF -> State MonadFree b
 */
function interpretScenario(callable $interpretAction, callable $interpretAssertion, ScenarioF $f)
{
    return matchPatterns([
        Given::class => function (Given $a): State {
            return State::of(function () use ($a) {
                return [$a->next, $a->state];
            });
        },
        When::class => function (When $a) use ($interpretAction): State {
            return State::of(function ($state) use ($interpretAction, $a) {
                $state = $interpretAction($a->action, $state);

                return [$a->next, $state];
            });
        },
        Then::class => function (Then $a) use ($interpretAssertion): State {
            return State::of(function ($state) use ($interpretAssertion, $a) {
                $ok = $interpretAssertion($a->assertion, $state);
                assert($ok, $a->assertion);

                return [$a->next, $state];
            });
        },
    ], $f);
}

const interpretAction = 'example\interpretAction';

/**
 * interpretAction :: List (String -> (a -> a)) -> String -> a -> a
 */
function interpretAction(array $patterns, string $s, $state)
{
    return matchRegexp(wrapWithState($patterns, $state), $s);
}

const interpretAssertion = 'example\interpretAssertion';

/**
 * interpretAssertion :: List (String -> (a -> Bool)) -> String -> a -> Bool
 */
function interpretAssertion($patterns, string $s, $state): bool
{
    return matchRegexp(wrapWithState($patterns, $state), $s);
}

function wrapWithState(array $patterns, $state)
{
    return array_map(function (callable $fn) use ($state) {
        return function () use ($fn, $state) {
            $args = push_([$state], func_get_args());

            return call_user_func_array($fn, $args);
        };
    }, $patterns);
}

function matchRegexp(array $patterns, $value = null)
{
    return curryN(2, function (array $patterns, $value) {
        foreach ($patterns as $pattern => $fn) {
            if (false !== preg_match($pattern, $value, $matches)) {
                return call_user_func_array($fn, array_slice($matches, 1));
            }
        }

        throw new \Exception(sprintf(
            'Cannot match "%s" to list of regexp %s',
            $value,
            implode(', ', array_keys($patterns))
        ));
    })(...func_get_args());
}

/**
 * Inspired by https://github.com/politrons/TestDSL
 */
class FreeBddStyleDSLTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_interpret_bdd_scenario()
    {
        $state = [
            'productsCount' => 0,
            'products' => [],
        ];

        $scenario =
            Given('Product in cart', $state)
                ->When("I add product 'coca-cola'")
                ->When("I add product 'milk'")
                ->Then("The number of products is '2'");

        $result = $scenario->Run([
            "/^I add product '(.*)'/" => function ($state, $productName) {
                $state['productsCount'] += 1;
                $state['products'][] = $productName;

                return $state;
            },
        ], [
            "/^The number of products is '(\d+)'/" => function ($state, int $expected) {
                return $state['productsCount'] === $expected;
            },
        ]);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('productsCount', $result);
        $this->assertArrayHasKey('products', $result);

        $this->assertEquals(2, $result['productsCount']);
        $this->assertEquals(['coca-cola', 'milk'], $result['products']);
    }
}
