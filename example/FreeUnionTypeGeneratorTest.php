<?php

declare(strict_types=1);

namespace example;

use FunctionalPHP\FantasyLand\Functor;
use Widmogrod\Monad\Free\MonadFree;
use Widmogrod\Monad\Free\Pure;
use Widmogrod\Monad\Identity;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\Stringg;
use Widmogrod\Useful\PatternMatcher;
use function Widmogrod\Functional\compose;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\prepend;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Free\liftF;
use function Widmogrod\Useful\matchPatterns;

/**
 * type UnionF _ next
 *  | Declare_ name [args] (a -> next)
 *  | Union_ a name [args] (a -> next)
 *  | Derived_ a [interfaces] (a -> next)
 */
interface UnionF extends Functor, PatternMatcher
{
}

class Declare_ implements UnionF
{
    private $name;
    private $args;
    private $next;

    public function __construct($name, array $args, callable $next)
    {
        $this->name = $name;
        $this->args = $args;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->name,
            $this->args,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->name, $this->args, $this->next);
    }
}

class Union_ implements UnionF
{
    private $a;
    private $name;
    private $args;
    private $next;

    public function __construct($a, $name, array $args, callable $next)
    {
        $this->a = $a;
        $this->name = $name;
        $this->args = $args;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->a,
            $this->name,
            $this->args,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->name, $this->args, $this->next);
    }
}


class Derived_ implements UnionF
{
    private $a;
    private $interfaces;
    private $next;

    public function __construct($a, array $interfaces, callable $next)
    {
        $this->a = $a;
        $this->interfaces = $interfaces;
        $this->next = $next;
    }

    /**
     * @inheritdoc
     */
    public function map(callable $function): Functor
    {
        return new self(
            $this->a,
            $this->interfaces,
            compose($function, $this->next)
        );
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->interfaces, $this->next);
    }
}

function data_(string $name, array $args): MonadFree
{
    return liftF(new Declare_($name, $args, Pure::of));
}

function declaree(MonadFree $data, Listt $mx): MonadFree
{
    return reduce(function (MonadFree $m, callable $next) {
        return $m->bind($next);
    }, $data, $mx);
}

function declareType(string $name, array $args, callable $first, callable ...$rest)
{
    $mx = fromIterable($rest);
    $mx = prepend($first, $mx);

    return declaree(data_($name, $args), $mx);
}

function type(string $name, array $args = [], $a = null)
{
    return curryN(3, function (string $name, array $args, $a): MonadFree {
        return liftF(new Union_($a, $name, $args, Pure::of));
    })(...func_get_args());
}

function derived(array $interface = [], $a = null)
{
    return curryN(2, function (array $interface, $a): MonadFree {
        return liftF(new Derived_($a, $interface, Pure::of));
    })(...func_get_args());
}

class GeneratorLazy
{
    public $declaration = [];

    public function generate(): string
    {
        $r = $this->generateInterface($this->declaration['interface'], $this->declaration['extends']);
        foreach ($this->declaration['classes'] as $c) {
            $r = $r->concat(
                $this->generateClass($c['name'], $c['args'], $this->declaration['interface'])
            );
        }

        return $r->extract();
    }

    public function generateInterface($name, array $extends): Stringg
    {
        if (!count($extends)) {
            return Stringg::of(sprintf(
                "interface %s {}\n",
                $name
            ));
        }

        return Stringg::of(sprintf(
            "interface %s extends %s {}\n",
            $name,
            join(', ', $extends)
        ));
    }

    public function generateClass($name, array $args, $interface): Stringg
    {
        $args = array_map(function ($name) {
            return is_array($name) ? $name[0] : $name;
        }, $args);
        $privates = array_reduce($args, function ($body, $name) {
            return $body . "private \$$name;\n";
        }, '');

        $set = array_reduce($args, function ($body, $name) {
            return $body . "\$this->$name = \$$name;\n";
        }, '');

        $const = array_reduce($args, function ($body, $name) {
            return $body . "\$$name,";
        }, '');
        $const = trim($const, ', ');

        $pattern = array_reduce($args, function ($body, $name) {
            return $body . "\$this->$name, ";
        }, '');
        $pattern = trim($pattern, ', ');

        $constructorWithProps = '%s
    public function __construct(%s) {
        %s 
    }';
        $constructorWithProps = sprintf($constructorWithProps, $privates, $const, $set);

        $constructorWithProps = count($args) ? $constructorWithProps : '';


        return Stringg::of(sprintf(
            "class %s implements %s {
    %s
    public function patternMatched(callable \$fn) {
        return \$fn(%s);
    }
}\n",
            $name,
            $interface,
            $constructorWithProps,
            $pattern
        ));
    }

    public function __toString()
    {
        return $this->generate();
    }
}

const interpretTypesAndGenerate = 'example\interpretTypesAndGenerate';

/**
 * @param  UnionF                                   $f
 * @return Identity
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function interpretTypesAndGenerate(UnionF $f): Identity
{
    return matchPatterns([
        Declare_::class => function (string $name, array $args, callable $next): Identity {
            $a = new GeneratorLazy();
            $a->declaration = [
                'interface' => $name,
                'extends' => [],
                'classes' => []
            ];

            return Identity::of($a)->map($next);
        },
        Union_::class => function (GeneratorLazy $a, string $name, array $args, callable $next): Identity {
            $a->declaration['classes'][] = ['name' => $name, 'args' => $args];

            return Identity::of($a)->map($next);
        },
        Derived_::class => function (GeneratorLazy $a, array $interfaces, callable $next): Identity {
            $a->declaration['extends'] = $interfaces;

            return Identity::of($a)->map($next);
        }
    ], $f);
};

class FreeUnionTypeGeneratorTest extends \PHPUnit\Framework\TestCase
{
    public function test_example_1()
    {
        // $interpret :: UnionF -> Identity Free a
        $interpret = function (UnionF $f): Identity {
            return matchPatterns([
                Declare_::class => function (string $name, array $args, callable $next): Identity {
                    return Identity::of([
                        'interface' => $name,
                        'extends' => [],
                        'classes' => []
                    ])
                        ->map($next);
                },
                Union_::class => function ($a, string $name, array $args, callable $next): Identity {
                    $a['classes'][] = ['name' => $name, 'args' => $args];

                    return Identity::of($a)->map($next);
                },
                Derived_::class => function ($a, array $interfaces, callable $next): Identity {
                    $a['extends'] = $interfaces;

                    return Identity::of($a)->map($next);
                }
            ], $f);
        };

        $declaration = declareType(
            'Maybe',
            ['a'],
            type('Just', ['a']),
            type('Nothing', []),
            derived(['\Widmogrod\Useful\PatternMatcher'])
        );

        $expected = [
            'interface' => 'Maybe',
            'extends' => ['\Widmogrod\Useful\PatternMatcher'],
            'classes' => [
                ['name' => 'Just', 'args' => ['a']],
                ['name' => 'Nothing', 'args' => []],
            ],
        ];

        $result = foldFree($interpret, $declaration, Identity::of);
        $this->assertEquals(Identity::of($expected), $result);
    }

    public function test_example_2()
    {
        $declaration = declareType(
            'Maybe',
            ['a'],
            type('Just', ['a']),
            type('Nothing', []),
            derived(['\Widmogrod\Useful\PatternMatcher'])
        );

        $expected = 'interface Maybe extends \Widmogrod\Useful\PatternMatcher {}
class Just implements Maybe {
    private $a;

    public function __construct($a) {
        $this->a = $a;
 
    }
    public function patternMatched(callable $fn) {
        return $fn($this->a);
    }
}
class Nothing implements Maybe {
    
    public function patternMatched(callable $fn) {
        return $fn();
    }
}
';

        $result = foldFree(interpretTypesAndGenerate, $declaration, Identity::of);
        $this->assertEquals($expected, $result->extract()->generate());
    }
}
