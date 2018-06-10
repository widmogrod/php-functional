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
use function Widmogrod\Functional\constt;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\foldr;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\map;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Functional\zip;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;
use function Widmogrod\Useful\match;
use const Widmogrod\Functional\identity;

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

interface Type
{
}

class TVar implements Type
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

    private static $data;

    private function __construct(\ArrayObject $data)
    {
        self::$data = $data;
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
        return fromIterable(array_keys($set::$data->getArrayCopy()));
    }

    public static function withValue($n): Set
    {
        return new static(new \ArrayObject([
            $n => true,
        ]));
    }

    public static function insert($value, Set $set): Listt
    {
        $new = clone $set::$data;
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
        $diffA = reduce(function (Set $acc, $value) use ($b) {
            return static::member($value, $b)
                ? $acc
                : static::insert($value, $acc);
        }, static::mempty(), static::toList($a));
        $diffB = reduce(function (Set $acc, $value) use ($a) {
            return static::member($value, $a)
                ? $acc
                : static::insert($value, $acc);
        }, static::mempty(), static::toList($b));

        return static::union($diffA, $diffB);
    }

    private static function member($value, Set $set): bool
    {
        return isset($set::$data[$value]);
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
    const delete = 'Map::delete';

    protected static $data;

    private function __construct(\ArrayObject $data)
    {
        self::$data = $data;
    }

    public static function mempty()
    {
        return new static(new \ArrayObject());
    }

    public static function elems(Map $map): Listt
    {
        return fromIterable(array_keys($map::$data->getArrayCopy()));
    }

    public static function union(Map $a, Map $b): Map
    {
        return reduce(function (Map $acc, $key) use ($b) {
            return static::insert($ksey, $b::$data[$key], $acc);
        }, $a, static::elems($b));
    }

    public static function map(callable $fn, Map $map): Map
    {
        return reduce(function (Map $acc, $key) use ($fn, $map) {
            return static::insert($key, $fn($map::$data[$key]), $acc);
        }, static::mempty(), static::elems($map));
    }

    public static function lookup($key, Map $map): Maybe
    {
        return isset($map::$data[$key])
            ? just($map::$data[$key])
            : nothing();
    }

    public static function delete($key, Map $map): Map
    {
        $new = clone $map::$data;
        unset($new[$key]);
        return new static($new);
    }

    public static function insert($key, $value, Map $map): Map
    {
        $new = clone $map::$data;
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
}

function lookup(Map $map, $key): Maybe
{
    return MaP::lookup($key, $map);
}

// type Subst = Map.Map String Type
class Subst extends Map implements PatternMatcher
{
    /**
     * should be used with conjuction
     * @param  callable $fn
     * @return mixed
     */
    public function patternMatched(callable $fn)
    {
        throw new Exception('not implemented');
        // TODO: Implement patternMatched() method.
    }
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
 * @param  Type|Listt|Scheme|TypeEnv $t
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
                    Nothing::class => constt($a),   // Nothing → TVar n
                ], lookup($s, $n));
            },
            TFun::class => function (Type $t1, Type $t2) use ($s) {
                return new TFun(apply($s, $t1), apply($s, $t2));
            },
            TBool::class => constt($a),
            TInt::class => constt($a),
            // instance Types Scheme where
            Scheme::class => function (Listt $vars, Type $t) use ($s) {
                // apply s (Scheme vars t) = Scheme vars (apply (foldr Map.delete s vars) t)
                return new Scheme(
                    $vars,
                    apply(foldr(Map::delete, $s, $vars), $t)
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

// data TIEnv = TIEnv { }
// data TIState = TIState{tiSupply :: Int }
// type TI a = ErrorT String (ReaderT TIEnv (StateT TIState IO)) a
//class TI

$increment = 0;

// instantiate :: Scheme → TI Type
function instantiate(Scheme $s)
{
    global $increment;

    // instantiate (Scheme vars t)
    //                  = do nvars ← mapM (λ   → newTyVar "a") vars
    //                      let s = Map.fromList (zip vars nvars)
    //                      return $ apply s t
    return match([
        Scheme::class => function (Listt $vars, Type $t) use (&$increment) {
            $nvars = map(function () use (&$increment) {
                return new TVar('$a' . (++$increment));
            }, $vars);

            $s = Subst::fromList(zip($vars, $nvars));
            return apply($s, $t);
        },
    ], $s);
}

/*
tiLit :: Lit → TI (Subst , Type )
tiLit (LInt ) = return (nullSubst, TInt)
tiLit (LBool   ) = return (nullSubst, TBool)
*/
/**
 * @param Lit $li
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function tiLit(Lit $li)
{
    return match([
        LInt::class => constt([nullSubst(), new TInt()]),
        LBool::class => constt([nullSubst(), new TBool()]),
    ], $li);
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
                        Nothing::class => function () {
                            throw new Exception('not implemented');
                        },
                        Just::class => function ($sigma) {
                            return [nullSubst(), instantiate($sigma)];
                        },
                    ], Map::lookup($n, $envMap));
                },
                ELit::class => function (Lit $l) {
                    return tiLit($l);
                },
                EAbs::class => function () {
                    throw new Exception('not implemented');
                },
                EApp::class => function () {
                    throw new Exception('not implemented');
                },
                ELet::class => function ($n, Exp $e1, Exp $e2) use ($env, $envMap) {
                    [$s1, $t1] = ti($env, $e1);
                    $t1 = generalize(apply($s1, $env), $t1);
                    $env = new TypeEnv(Map::insert($n, $t1, $envMap));
                    [$s2, $t2] = ti($env, $e2);
                    return [composeSubst($s1, $s2), $t2];
                },
            ], $e);
        },
    ], $env);
}

/**
 * // typeInference :: Map.Map String Scheme → Exp → TI Type
 * @param \example\Map $env
 * @param Exp $e
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function typeInference(Map $env, Exp $e)
{
    [$s, $t] = ti(new TypeEnv($env), $e);
    return apply($s, $t);
}

/**
 * @param Type $t
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function showType(Type $t)
{
    return match([
        TInt::class => constt('Int'),
        TBool::class => constt('Bool'),
        TVar::class => identity,
        TFun::class => function (Type $t1, Type $t2) {
            return sprintf('(%s -> %s)', showType($t1), showType($t2));
        }
    ], $t);
}

/**
 * @param Type $t
 * @return mixed
 * @throws \Widmogrod\Useful\PatternNotMatchedError
 */
function showImpl(Exp $e)
{
    return match([
        ELit::class => match([
            LInt::class => identity,
            LBool::class => identity,
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
    public function test(Exp $expression, $expected)
    {
        echo showImpl($expression), "\n";
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
                'expected' => 'a1 -> a1',
            ],
//            // e1 = ELet "id" (EAbs "x" (EVar "x")) (EApp (EVar "id") (EVar "id"))
//            'let id = (x -> x) in id id' => [
//                'expression' => new ELet(
//                    'id',
//                    new EAbs(
//                        'x',
//                        new EVar('x')
//                    ),
//                    new EApp(
//                        new EVar('id'),
//                        new EVar('id')
//                    )
//                ),
//                'expected' => 'a3 -> a3',
//            ],
//            // e2 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EVar "id") (EVar "id"))
//            'let id = (x -> let y = x in y) in id id ' => [
//                'expression' => new ELet(
//                    'x',
//                    new EAbs(
//                        'x',
//                        new ELet(
//                            'y',
//                            new EVar('x'),
//                            new EVar('y')
//                        )
//                    ),
//                    new EApp(
//                        new EVar('id'),
//                        new EVar('id')
//                    )
//                ),
//                'expected' => 'a3 -> a3',
//            ],
//            // e3 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EApp (EVar "id") (EVar "id")) (ELit (LInt 2)))
//            'let id = (x -> let y = x in y) in ((id id) 2)' => [
//                'expression' => new ELet(
//                    'id',
//                    new EAbs(
//                        'x',
//                        new ELet(
//                            'y',
//                            new EVar('x'),
//                            new EVar('y')
//                        )
//                    ),
//                    new EApp(
//                        new EApp(
//                            new EVar('id'),
//                            new EVar('id')
//                        ),
//                        new ELit(new LInt(2))
//                    )
//                ),
//                'expected' => 'Int',
//            ],
//            // e4 = ELet "id" (EAbs "x" (EApp (EVar "x") (EVar "x"))) (EVar "id")
//            'let id = (x -> (x x)) in id' => [
//                'expression' => new ELet(
//                    'id',
//                    new EAbs(
//                        'x',
//                        new EApp(
//                            new EVar('x'),
//                            new EVar('x')
//                        )
//                    ),
//                    new EVar('id')
//                ),
//                'expected' => 'error: occur check fails: a0 vs. a0 -> a1',
//            ],
//            // e5 = EAbs "m" (ELet "y" (EVar "m")
//            //       (ELet "x" (EApp (EVar "y") (ELit (LBool True)))
//            //          (EVar "x")))
//            '(m -> let y = m in let x = (y true) in x)' => [
//                'expression' => new EAbs(
//                    'm',
//                    new ELet(
//                        'y',
//                        new EVar('m'),
//                        new ELet(
//                            'x',
//                            new EApp(
//                                new EVar('y'),
//                                new ELit(new LBool(true))
//                            ),
//                            new EVar('x')
//                        )
//                    )
//                ),
//                'expected' => '(Bool -> a1) -> a1',
//            ],
//            // e6 = EApp (ELit (LInt 2)) (ELit (LInt 2))
//            '(2 2)' => [
//                'expression' => new EApp(
//                    new ELit(new LInt(2)),
//                    new ELit(new LInt(2))
//                ),
//                'expected' => 'error: types do not unify: Int vs. Int -> a0',
//            ],
            // e7 = ELet "id" (EAbs "x" (EVar "y")) (EVar "id")
//            'let id = (x -> y) in id)' => [
//                'expression' => new ELet(
//                    'id',
//                    new EAbs("x", new EVar("y")),
//                    new EVar("id")
//                ),
//                'expected' => 'error: unbound variable: y',
//            ],
        ];
    }
}
