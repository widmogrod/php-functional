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

use FunctionalPHP\FantasyLand\Monoid;
use FunctionalPHP\FantasyLand\Semigroup;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Maybe;
use Widmogrod\Monad\Maybe\Nothing;
use Widmogrod\Primitive\Listt;
use Widmogrod\Useful\PatternMatcher;
use function Widmogrod\Functional\constt;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\foldr;
use function Widmogrod\Functional\map;
use function Widmogrod\Useful\match;
use const Widmogrod\Functional\identity;

interface Exp
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

interface Lit
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
class Scheme
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

class Set implements Monoid
{
    /**
     * @inheritdoc
     */
    public static function mempty()
    {
        // TODO: Implement mempty() method.
    }

    public static function fromList(Listt $l)
    {
    }

    public static function toList(Set $set): Listt
    {
    }

    /**
     * @inheritdoc
     */
    public function concat(Semigroup $value): Semigroup
    {
        // TODO: Implement concat() method.
    }
}

function union(Set $a, Set $b): Set
{
    // TODO: Implement union() method.
}

function difference(Set $a, Set $b): Set
{
    // TODO: Implement difference() method.
}

class Map
{
    const delete = 'Map::delete';

    public static function elems(Map $map): Listt
    {
    }

    /**
     * @return $this
     */
    public static function union(Map $a, Map $b)
    {
    }

    /**
     * @return $this
     */
    public static function map(callable $fn, Map $map)
    {
    }

    public static function lookup($key): Maybe
    {
    }

    /**
     * @return $this
     */
    public static function delete($key, Map $map)
    {
    }
}

function lookup(Map $map, $key): Maybe
{
    return $map->lookup($key);
}

// type Subst = Map.Map String Type
class Subst extends Map implements PatternMatcher
{
    private $vars;
    private $type;

    public function __construct(Listt $vars, Type $type)
    {
        $this->vars = $vars;
        $this->type = $type;
    }


    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->vars, $this->type);
    }
}


// nullSubst :: Subst
// nullSubst = Map.empty
function nullSubst(): Subst
{
    return Subst::mempty();
}

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
            return new Set($n);
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

class FreeMonadTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideExamples
     */
    public function test(Exp $expression)
    {
        $this->assertInstanceOf(Exp::class, $expression);
    }

    public function provideExamples()
    {
        return [
            // e0 = ELet "id" (EAbs "x" (EVar "x")) (EVar "id")
            'let id = (x -> x) in id)' => [
                'expression' => new ELet(
                    'id',
                    new EAbs("x", new EVar("x")),
                    new EVar("id")
                ),
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
            ],
            // e2 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EVar "id") (EVar "id"))
            'let id = (x -> let y = x in y) in id id ' => [
                'expression' => new ELet(
                    'x',
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
            ],
            // e3 = ELet "id" (EAbs "x" (ELet "y" (EVar "x") (EVar "y"))) (EApp (EApp (EVar "id") (EVar "id")) (ELit (LInt 2)))
            'let id = (x -> let y = x in y) in ((id id) 2)' => [
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
            ],
            // e4 = ELet "id" (EAbs "x" (EApp (EVar "x") (EVar "x"))) (EVar "id")
            'let id = (x -> (x x)) in id' => new ELet(
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
            // e5 = EAbs "m" (ELet "y" (EVar "m")
            //       (ELet "x" (EApp (EVar "y") (ELit (LBool True)))
            //          (EVar "x")))
            '(m -> let y = m in let x = (y true) in x)' => [
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
                )
            ],
            // e6 = EApp (ELit (LInt 2)) (ELit (LInt 2))
            '(2 2)' => [
                'expression' => new EApp(
                    new ELit(new LInt(2)),
                    new ELit(new LInt(2))
                ),
            ],
        ];
    }
}
