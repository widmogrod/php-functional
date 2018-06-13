<?php

declare(strict_types=1);

namespace example;

/*
data Exp
    = EVar String
    | ELit Lit
    | EApp Exp Exp
    | EAbs String Exp
    | ELet String Exp Exp
    deriving (Eq,Ord)

data Lit
    = LInt Integer
    | LBool Bool deriving (Eq,Ord)

data Type
    = TVar String
    | TInt
    | TBool
    | TFun Type Type
    deriving (Eq,Ord)

data Scheme = Scheme [ String ] Type

*/

use PHPUnit\Runner\Exception;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Maybe;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Primitive\Listt;
use Widmogrod\Useful\PatternMatcher;
use function Widmogrod\Functional\concatM;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\foldr;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\map;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Functional\zip;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;
use function Widmogrod\Useful\match;
use const Widmogrod\Functional\identity;
use const Widmogrod\Useful\any;

interface Exp extends PatternMatcher
{
}

class EVar implements Exp
{
    private $string;

    public function __construct($string)
    {
        $this->string = $string;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->string);
    }
}

class ELit implements Exp
{
    private $lit;

    public function __construct(Lit $lit)
    {
        $this->lit = $lit;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->lit);
    }
}

class EApp implements Exp
{
    private $exp1;
    private $exp2;

    public function __construct(Exp $exp1, Exp $exp2)
    {
        $this->exp1 = $exp1;
        $this->exp2 = $exp2;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->exp1, $this->exp2);
    }
}

class EAbs implements Exp
{
    private $string;
    private $exp;

    public function __construct($string, Exp $exp)
    {
        $this->string = $string;
        $this->exp = $exp;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->string, $this->exp);
    }
}

class ELet implements Exp
{
    private $string;
    private $exp1;
    private $exp2;

    public function __construct($string, Exp $exp1, Exp $exp2)
    {
        $this->string = $string;
        $this->exp1 = $exp1;
        $this->exp2 = $exp2;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->string, $this->exp1, $this->exp2);
    }
}

interface Lit extends PatternMatcher
{
}

class LInt implements Lit
{
    private $int;

    public function __construct(int $int)
    {
        $this->int = $int;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->int);
    }
}

class LBool implements Lit
{
    private $bool;

    public function __construct(bool $bool)
    {
        $this->bool = $bool;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->bool);
    }
}

interface Type extends PatternMatcher
{
}

class TVar implements Type
{
    private $string;

    public function __construct(string $string)
    {
        $this->string = $string;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->string);
    }
}

class TInt implements Type
{
    public function patternMatched(callable $fn)
    {
        return $fn();
    }
}

class TBool implements Type
{
    public function patternMatched(callable $fn)
    {
        return $fn();
    }
}

class TFun implements Type
{
    private $type1;
    private $type2;

    public function __construct(Type $type1, Type $type2)
    {
        $this->type1 = $type1;
        $this->type2 = $type2;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->type1, $this->type2);
    }
}

// data Scheme = Scheme [ String ] Type
class Scheme implements PatternMatcher
{
    /**
     * @var array
     */
    private $strings;
    private $type;

    public function __construct(Listt $strings, Type $type)
    {
        $this->strings = $strings;
        $this->type = $type;
    }

    public function patternMatched(callable $fn)
    {
        return $fn($this->strings, $this->type);
    }
}

class Set
{
    const union = 'example\\Set::union';

    private $data;

    private function __construct(\ArrayObject $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        return new static(new \ArrayObject());
    }

    public static function fromList(Listt $l)
    {
        return reduce(function (Set $acc, $value) {
            return $acc::insert($value, $acc);
        }, static::mempty(), $l);
    }

    public static function toList(Set $set): Listt
    {
        return fromIterable(array_keys($set->data->getArrayCopy()));
    }

    public static function withValue($n): Set
    {
        return new static(new \ArrayObject([
            $n => true,
        ]));
    }

    public static function insert($value, Set $set): Set
    {
        $new = clone $set->data;
        $new[$value] = true;

        return new static($new);
    }

    public static function union(Set $a, Set $b): Set
    {
        return self::fromList(concatM(
            static::toList($a),
            static::toList($b)
        ));
    }

    public static function difference(Set $a, Set $b): Set
    {
        return reduce(function (Set $acc, $value) use ($b) {
            return static::member($value, $b)
                ? $acc
                : static::insert($value, $acc);
        }, static::mempty(), static::toList($a));
    }

    public static function member($value, Set $set): bool
    {
        return isset($set->data[$value]);
    }
}

function union(Set $a, Set $b): Set
{
    return Set::union($a, $b);
}

function difference(Set $a, Set $b): Set
{
    return Set::difference($a, $b);
}

class Map
{
    private $data;

    private function __construct(\ArrayObject $data)
    {
        $this->data = $data;
    }

    public static function mempty()
    {
        return new static(new \ArrayObject());
    }

    public static function elems(Map $map): Listt
    {
        return fromIterable(array_values($map->data->getArrayCopy()));
    }


    public static function keys(Map $map): Listt
    {
        return fromIterable(array_keys($map->data->getArrayCopy()));
    }

    public static function union(Map $a, Map $b): Map
    {
        return reduce(function (Map $acc, $key) use ($b) {
            return static::insert($key, $b->data[$key], $acc);
        }, $a, static::keys($b));
    }

    public static function map(callable $fn, Map $map): Map
    {
        return reduce(function (Map $acc, $key) use ($fn, $map) {
            return static::insert($key, $fn($map->data[$key]), $acc);
        }, static::mempty(), static::keys($map));
    }

    public static function lookup($key, Map $map): Maybe
    {
        return isset($map->data[$key])
            ? just($map->data[$key])
            : nothing();
    }

    public static function delete($key, Map $map): Map
    {
        if (isset($map->data[$key])) {
            $new = clone $map->data;
            unset($new[$key]);

            return new static($new);
        }

        return $map;
    }

    public static function insert($key, $value, Map $map): Map
    {
        $new = clone $map->data;
        $new[$key] = $value;

        return new static($new);
    }

    public static function fromList(Listt $list)
    {
        return reduce(function (Map $acc, $tuple) {
            [$key, $value] = $tuple;

            return static::insert($key, $value, $acc);
        }, static::mempty(), $list);
    }

    public static function singleton($u, $t)
    {
        return static::insert($u, $t, static::mempty());
    }
}

function lookup(Map $map, string $key): Maybe
{
    return $map::lookup($key, $map);
}

// type Subst = Map.Map String Type
class Subst extends Map
{
}

const nullSubst = 'example\nullSubst';

// nullSubst :: Subst
// nullSubst = Map.empty
function nullSubst(): Subst
{
    return Subst::mempty();
}

const composeSubst = 'example\composeSubst';

// composeSubst :: Subst → Subst → Subst
// composeSubst s1 s2 = (Map.map (apply s1) s2) ‘Map.union‘ s1
function composeSubst(Subst $s1, Subst $s2): Subst
{
    return Subst::union(
        Subst::map(apply($s1), $s2),
        $s1
    );
}

// newtype TypeEnv = TypeEnv (Map.Map String Scheme)
class TypeEnv implements PatternMatcher
{
    private $env;

    public function __construct(Map $env)
    {
        $this->env = $env;
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->env);
    }
}

// remove :: TypeEnv → String → TypeEnv
// remove (TypeEnv env) var = TypeEnv (Map.delete var env)

function remove(TypeEnv $env, $var): TypeEnv
{
    return new TypeEnv(Map::delete($var, $env));
}

// generalize :: TypeEnv → Type → Scheme
// generalize env t = Scheme vars t
//      where vars = Set.toList ((ftv t) \ (ftv env))
function generalize(TypeEnv $env, Type $t): Scheme
{
    return new Scheme(
        Set::toList(difference(ftv($t), ftv($env))),
        $t
    );
}

const ftv = 'example\ftv';

/**
 * ftv :: a → Set.Set String
 *
 * @param  Type|Listt|Scheme|TypeEnv                $t
 * @return Set
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function ftv($t): Set
{
    return match([
        // instance Types Type where
        TVar::class => function ($n): Set {
            return Set::withValue($n);
        },
        TBool::class => function (): Set {
            return Set::mempty();
        },
        TInt::class => function (): Set {
            return Set::mempty();
        },
        TFun::class => function (Type $a, Type $b): Set {
            return union(ftv($a), ftv($b));
        },
        // instance Types Scheme where
        Scheme::class => function (Listt $vars, Type $t): Set {
            // ftv (Scheme vars t) = (ftv t) \ (Set.fromList vars)
            return difference(ftv($t), Set::fromList($vars));
        },
        // instance Types a ⇒ Types [a] where
        Listt::class => function (Listt $l): Set {
            // ftv l = foldr Set.union ∅ (map ftv l)
            return foldr(Set::union, Set::mempty(), map(ftv, $l));
        },
        // instance Types TypeEnv where
        TypeEnv::class => function (Map $env): Set {
            // ftv (TypeEnv env) = ftv (Map.elems env)
            return ftv(Map::elems($env));
        },
    ], $t);
}

/**
 * // apply :: Subst → a → a
 *
 * @param Subst $s
 * @param Type|Scheme|Listt|TypeEnv ?$a
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function apply(Subst $s, $a = null)
{
    return curryN(2, function (Subst $s, $a) {
        return match([
            // instance Types Type where
            TVar::class => function ($n) use ($s, $a) {
                return match([
                    Just::class => identity,        // Justt → t
                    Nothing::class => function () use ($a) {
                        return $a;
                    },   // Nothing → TVar n
                ], lookup($s, $n));
            },
            TFun::class => function (Type $t1, Type $t2) use ($s) {
                return new TFun(apply($s, $t1), apply($s, $t2));
            },
            TBool::class => function () use ($a) {
                return $a;
            },
            TInt::class => function () use ($a) {
                return $a;
            },
            // instance Types Scheme where
            Scheme::class => function (Listt $vars, Type $t) use ($s) {
                // apply s (Scheme vars t) = Scheme vars (apply (foldr Map.delete s vars) t)
                return new Scheme(
                    $vars,
                    apply(foldr(function (string $var, Subst $sub) {
                        return Subst::delete($var, $sub);
                    }, $s, $vars), $t)
                );
            },
            // instance Types a ⇒ Types [a] where
            Listt::class => function (Listt $l) use ($s) {
                // apply s = map (apply s)
                return map(apply($s), $l);
            },
            // instance Types TypeEnv where
            TypeEnv::class => function (Map $env) use ($s) {
                // apply s (TypeEnv env) = TypeEnv (Map.map (apply s) env)
                return new TypeEnv(Map::map(apply($s), $env));
            },
        ], $a);
    })(...func_get_args());
}

$increment = 0;

function newVar($name)
{
    global $increment;

    return new TVar(sprintf('%s%d', $name, ++$increment));
}

// instantiate :: Scheme → TI Type
function instantiate(Scheme $s)
{
    return match([
        Scheme::class => function (Listt $vars, Type $t) {
            $nvars = map(function () {
                return newVar('a');
            }, $vars);

            $s = Subst::fromList(zip($vars, $nvars));

            return apply($s, $t);
        },
    ], $s);
}

/**
 * @param  Lit                                      $li
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function tiLit(Lit $li)
{
    return match([
        LInt::class => function () {
            return [nullSubst(), new TInt()];
        },
        LBool::class => function () {
            return [nullSubst(), new TBool()];
        },
    ], $li);
}

/**
 * @param  Type                                     $a
 * @param  Type                                     $b
 * @return Subst
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function mgu(Type $a, Type $b): Subst
{
    return match([
        [[TFun::class, TFun::class], function (Type $l1, Type $r1, Type $l2, Type $r2) {
            $s1 = mgu($l1, $l2);
            $s2 = mgu(apply($s1, $r1), apply($s1, $r2));

            return composeSubst($s1, $s2);
        }],
        [[TInt::class, TInt::class], function () {
            return Subst::mempty();
        }],
        [[TBool::class, TBool::class], function () {
            return Subst::mempty();
        }],
        [[TVar::class, any], function ($n, Type $t) {
            return varBind($n, $t);
        }],
        [[any, TVar::class], function (Type $t, $n) {
            return varBind($n, $t);
        }],
        [[any, any], function (Type $a, Type $b) {
            $message = sprintf('types do not unify: %s != %s', showType($a), showType($b));
            throw new Exception($message);
        }],
    ], [$a, $b]);
}

/**
 * @param $u
 * @param  Type                                     $t
 * @return Subst
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function varBind($u, Type $t): Subst
{
    return match([
        TVar::class => function ($n) use ($u, $t) {
            if ($n === $u) {
                return nullSubst();
            }

            return Subst::singleton($u, $t);
        },
        any => function (Type $t) use ($u) {
            if (Set::member($u, ftv($t))) {
                $message = 'occurs check fails: %s vs %s ftv(%s)';
                $message = sprintf($message, $u, dump($t), dump(ftv($t)));
                throw new Exception($message);
            }

            return Subst::singleton($u, $t);
        },
    ], $t);
}

/**
 * ti :: TypeEnv → Exp → TI (Subst , Type )
 *
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function ti(TypeEnv $env, Exp $e)
{
    return match([
        TypeEnv::class => function (Map $envMap) use ($env, $e) {
            return match([
                EVar::class => function ($n) use ($envMap) {
                    return match([
                        Nothing::class => function () use ($n) {
                            throw new Exception('unbound variable ' . $n);
                        },
                        Just::class => function ($sigma) {
                            return [nullSubst(), instantiate($sigma)];
                        },
                    ], Map::lookup($n, $envMap));
                },
                ELit::class => function (Lit $l) {
                    return tiLit($l);
                },
                EAbs::class => function ($n, Exp $e) use ($env, $envMap) {
                    $tv = newVar('a');
                    $sk = new Scheme(fromNil(), $tv);
                    $env = new TypeEnv(Map::insert($n, $sk, $envMap));

                    [$s1, $t1] = ti($env, $e);

                    return [$s1, new TFun(apply($s1, $tv), $t1)];
                },
                EApp::class => function (Exp $e1, Exp $e2) use ($env, $envMap) {
                    $tv = newVar('a');
                    [$s1, $t1] = ti($env, $e1);
                    [$s2, $t2] = ti(apply($s1, $env), $e2);
                    $s3 = mgu(apply($s2, $t1), new TFun($t2, $tv));

                    return [composeSubst($s3, composeSubst($s2, $s1)), apply($s3, $tv)];
                },
                ELet::class => function ($n, Exp $e1, Exp $e2) use ($env, $envMap) {
                    [$s1, $t1] = ti($env, $e1);
                    $sk = generalize(apply($s1, $env), $t1);
                    $env = new TypeEnv(Map::insert($n, $sk, $envMap));
                    [$s2, $t2] = ti(apply($s1, $env), $e2);

                    return [composeSubst($s1, $s2), $t2];
                },
            ], $e);
        },
    ], $env);
}

const dump = 'example\dump';

function dump($a)
{
    return match([
        Subst::class => function (Subst $s) {
            return iterator_to_array(Subst::elems($s));
        },
        TInt::class => function () use ($a) {
            return showType($a);
        },
        TBool::class => function () use ($a) {
            return showType($a);
        },
        TFun::class => function () use ($a) {
            return showType($a);
        },
        TVar::class => function () use ($a) {
            return showType($a);
        },
        TypeEnv::class => function (Map $env) {
            return sprintf('TypeEnv(%s)', dump($env));
        },

        Scheme::class => function (Listt $vars, Type $t) {
            return sprintf(
                'Scheme([%s], %s)',
                implode(',', iterator_to_array($vars)),
                dump($t)
            );
        },
        Listt::class => function (Listt $l) {
            return sprintf('[%s]', implode(', ', iterator_to_array($l)));
        },
        Subst::class => function (Map $map) {
            return dump(map(function (array $tuple) {
                [$a, $b] = $tuple;

                return sprintf('(%s, %s)', $a, $b);
            }, zip(
                Subst::keys($map),
                Subst::elems(Map::map(dump, $map))
            )));
        },
        Map::class => function (Map $map) {
            return dump(map(function (array $tuple) {
                [$a, $b] = $tuple;

                return sprintf('(%s, %s)', $a, $b);
            }, zip(
                Map::keys($map),
                Map::elems(Map::map(dump, $map))
            )));
        },
        Set::class => function (Set $s) {
            return sprintf('{%s}', implode(',', iterator_to_array(Set::toList($s))));
        },
    ], $a);
}

/**
 * // typeInference :: Map.Map String Scheme → Exp → TI Type
 * @param  \example\Map                             $env
 * @param  Exp                                      $e
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function typeInference(Map $env, Exp $e)
{
    [$s, $t] = ti(new TypeEnv($env), $e);

    return apply($s, $t);
}

/**
 * @param  Type                                     $t
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function showType(Type $t)
{
    return match([
        TInt::class => function () {
            return 'Int';
        },
        TBool::class => function () {
            return 'Bool';
        },
        TVar::class => function ($n) {
            return $n;
        },
        TFun::class => function (Type $t1, Type $t2) {
            return sprintf('(%s -> %s)', showType($t1), showType($t2));
        }
    ], $t);
}

/**
 * @param  Type                                     $t
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function showImpl(Exp $e)
{
    return match([
        ELit::class => match([
            LInt::class => identity,
            LBool::class => function ($val) {
                return $val ? 'true' : 'false';
            },
        ]),
        EVar::class => function ($n) {
            return $n;
        },
        ELet::class => function ($n, Exp $e1, Exp $e2) {
            return sprintf('let %s = %s in %s', $n, showImpl($e1), showImpl($e2));
        },
        EApp::class => function (Exp $e1, Exp $e2) {
            return sprintf('%s %s', showImpl($e1), showImpl($e2));
        },
        EAbs::class => function ($arg, Exp $e) {
            return sprintf('(%s -> %s)', $arg, showImpl($e));
        }
    ], $e);
}

class FreeMonadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function test(Exp $expression, $expected, $error = null)
    {
        // TODO fix this hack
        global $increment;
        $increment = 0;

        if ($error) {
            $this->expectExceptionMessage($error);
        }

        // echo showImpl($expression), "\n";
        $this->assertInstanceOf(Exp::class, $expression);
        $result = typeInference(Map::mempty(), $expression);
        $this->assertEquals($expected, showType($result));
    }

    public function provideExamples()
    {
        return [
            '2' => [
                'expression' => new ELit(new LInt(2)),
                'expected' => 'Int',
            ],
            '(x -> x)' => [
                'expression' => new EAbs(
                    'x',
                    new EVar('x')
                ),
                'expected' => '(a1 -> a1)',
            ],
            'let id = (x -> let y = (z -> let v = z in v) in y) in id id' => [
                'expression' => new ELet(
                    'id',
                    new EAbs(
                        'x',
                        new ELet(
                            'y',
                            new EAbs(
                                'z',
                                new ELet(
                                    'v',
                                    new EVar('z'),
                                    new EVar('v')
                                )
                            ),
                            new EVar('y')
                        )
                    ),
                    new EApp(
                        new EVar('id'),
                        new EVar('id')
                    )
                ),
                'expected' => '(a8 -> a8)',
            ],
            'let id = (x -> true) in id' => [
                'expression' => new ELet(
                    'id',
                    new EAbs("x", new ELit(new LBool(true))),
                    new EVar("id")
                ),
                'expected' => '(a2 -> Bool)',
            ],
            'let id = 2 in id' => [
                'expression' => new ELet(
                    'id',
                    new ELit(new LInt(2)),
                    new EVar("id")
                ),
                'expected' => 'Int',
            ],
            // e0 = ELet "id" (EAbs "x" (EVar "x")) (EVar "id")
            'let id = (x -> x) in id)' => [
                'expression' => new ELet(
                    'id',
                    new EAbs("x", new EVar("x")),
                    new EVar("id")
                ),
                'expected' => '(a2 -> a2)',
            ],
            // e1 = ELet "id" (EAbs "x" (EVar "x")) (EApp (EVar "id") (EVar "id"))
            'let id = (x -> x) in id id' => [
                'expression' => new ELet(
                    'id',
                    new EAbs(
                        'x',
                        new EVar('x')
                    ),
                    new EApp(
                        new EVar('id'),
                        new EVar('id')
                    )
                ),
                'expected' => '(a5 -> a5)',
            ],
            // e2 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EVar "id") (EVar "id"))
            'let id = (x -> let y = x in y) in id id ' => [
                'expression' => new ELet(
                    'id',
                    new EAbs(
                        'x',
                        new ELet(
                            'y',
                            new EVar('x'),
                            new EVar('y')
                        )
                    ),
                    new EApp(
                        new EVar('id'),
                        new EVar('id')
                    )
                ),
                'expected' => '(a5 -> a5)',
            ],
            // e3 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EApp (EVar "id") (EVar "id")) (ELit (LInt 2)))
            'let id = (x -> let y = x in y) in id id 2' => [
                'expression' => new ELet(
                    'id',
                    new EAbs(
                        'x',
                        new ELet(
                            'y',
                            new EVar('x'),
                            new EVar('y')
                        )
                    ),
                    new EApp(
                        new EApp(
                            new EVar('id'),
                            new EVar('id')
                        ),
                        new ELit(new LInt(2))
                    )
                ),
                'expected' => 'Int',
            ],
            // e4 = ELet "id" (EAbs "x" (EApp (EVar "x") (EVar "x"))) (EVar "id")
            'let id = (x -> x x) in id' => [
                'expression' => new ELet(
                    'id',
                    new EAbs(
                        'x',
                        new EApp(
                            new EVar('x'),
                            new EVar('x')
                        )
                    ),
                    new EVar('id')
                ),
                'expected' => null,
                'error' => 'occurs check fails: a1 vs (a1 -> a2)'
            ],
            // e5 = EAbs "m" (ELet "y" (EVar "m")
            //       (ELet "x" (EApp (EVar "y") (ELit (LBool True)))
            //          (EVar "x")))
            '(m -> let y = m in let x = y true in x)' => [
                'expression' => new EAbs(
                    'm',
                    new ELet(
                        'y',
                        new EVar('m'),
                        new ELet(
                            'x',
                            new EApp(
                                new EVar('y'),
                                new ELit(new LBool(true))
                            ),
                            new EVar('x')
                        )
                    )
                ),
                'expected' => '((Bool -> a2) -> a2)',
            ],
            // e6 = EApp (ELit (LInt 2)) (ELit (LInt 2))
            '2 2' => [
                'expression' => new EApp(
                    new ELit(new LInt(2)),
                    new ELit(new LInt(2))
                ),
                'expected' => null,
                'error' => 'types do not unify: Int != (Int -> a1)',
            ],
            // e7 = ELet "id" (EAbs "x" (EVar "y")) (EVar "id")
            'let id = (x -> y) in id' => [
                'expression' => new ELet(
                    'id',
                    new EAbs("x", new EVar("y")),
                    new EVar("id")
                ),
                'expected' => null,
                'error' => 'unbound variable y',
            ],
        ];
    }
}
