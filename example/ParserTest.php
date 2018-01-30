<?php

declare(strict_types=1);

namespace example;

use function Widmogrod\Monad\Free\foldFree;
use Widmogrod\Monad\Identity;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Maybe;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\Stringg;
use const Widmogrod\Functional\concatM;
use const Widmogrod\Functional\fromValue;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\concatM;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromValue;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Functional\span;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;

// Some dependencies are needed
require_once __DIR__ . '/FreeCalculatorTest.php';

/**
 *  ParserF a next
 *      = RuleChar a (a -> next)
 *      | RuleNumbers (a -> next)
 *      | Grammar [Def] (a -> next)
 *
 *      | Ref name (a -> next)
 *      | Def name Ref (a -> next)
 *      | OneOf [Rule] (a -> next)
 *      | AllOf [Rule] (a -> next)
 *      | Parse Grammar (a -> next)
 *
 *
 *      | Token  (Either (matched, rest) -> next)
 *
 *      | Lazy (_ -> MonadFree) (MonadFree -> next)
 *
 *      | ParseInput (_ -> next)
 *      | ConsumeOne   stream (char -> Bool) (Either ((matched, stream) -> next) (stream -> next))
 *      | ConsumeWhile stream (char -> Bool) (Either ((matched, stream) -> next) (stream -> next))
 *
 *
 *      | Match [char] (char -> Bool) ([matched] -> token) ([Either token error, [rest-char]] -> next)
 *      | AllOf [char] [Match] ([matched] -> token) ([Either token error, rest] -> next)
 *
 *      | OneOf [Match]
 *
 *       ... ... ... ...
 *
 *      match :: (a -> Bool) -> [a] -> Maybe ([a], [a])
 *
 *      numbers :: [a] -> Maybe [a]
 *      numbers = match isNumber
 *
 *      tokenize :: Maybe [a] -> (...a -> b) -> Maybe b
 *
 *      allof :: [Maybe a] -> ([a] -> b) -> Maybe b
 *      oneof :: [Maybe a] -> Maybe a
 *
 *      tokenize' :: ([a] -> Maybe ([a], [a])) -> ([a] -> b) -> [a] -> Maybe (b, [a])
 *      allof' :: ([([a] -> Maybe (b, [a]))] -> ([b] -> b) -> [a] -> Maybe (b, [a])
 *      oneof' :: ([([a] -> Maybe (b, [a]))] -> [a] -> Maybe (b, [a])
 *
 *      reduce :: (a -> b -> a) a [b]
 *
 *      foldr :: (a -> b -> b) -> b -> t a -> b
 *      foldl :: (b -> a -> b) -> b -> t a -> b
 *
 *
 *      literal  = tokenize numbers (\ys -> atoi(concat(ys)))
 *      operator = oneof' [tokenize (char "+") OpSum
 *                        ,tokenize (char "*") OpMul]
 *
 *      denest :: ([a] -> (b, [a]))) -> ([a] -> Maybe(b, [a])))
 *
 *
 *
 *       Stream s u m
 */

// match :: (a -> Bool) -> [a] -> Maybe ([a], [a])
function matchP(callable $predicate, Listt $a = null)
{
    return curryN(2, function (callable $predicate, Listt $a) {
        [$matched, $rest] = span($predicate, $a);

        return length($matched) > 0
            ? just([$matched, $rest])
            : nothing();
    })(...func_get_args());
}

const numbersP = 'example\\numbersP';

// numbers :: [a] -> Maybe ([a], [a])
function numbersP(Listt $a)
{
    return matchP(function (Stringg $s) {
        return \is_numeric($s->extract());
    }, $a);
}

// char :: Char -> [a] -> Maybe ([a], [a])
function charP(string $char, Listt $a = null)
{
    return curryN(2, function (string $char, Listt $a) {
        return matchP(function (Stringg $s) use ($char) {
            // TODO this should be called once
            return $s->extract() === $char;
        }, $a);
    })(...func_get_args());
}

function maybeMapFirst(callable $fn)
{
    return function ($result) use ($fn) {
        [$matched, $rest] = $result;

        return just([
            $fn($matched),
            $rest
        ]);
    };
}

// tokenize' :: ([a] -> Maybe ([a], [a])) -> (a -> b) -> [a] -> Maybe (b, [a])
function tokenizeP(callable $matcher, callable $map = null, Listt $a = null)
{
    return curryN(3, function (callable $matcher, callable $map, Listt $a) {
        return bind(maybeMapFirst($map), $matcher($a));
    })(...func_get_args());
}

// allof' :: ([([a] -> Maybe b)] -> ([b] -> b) -> [a] -> Maybe b
function allOfP(Listt $matchers, callable $map = null, Listt $a = null)
{
    return curryN(3, function (Listt $matchers, callable $map, Listt $a) {
        $result = reduce(function (?Maybe $b, callable $matcher) use ($a) {
            return $b instanceof Just
                ? $b->bind(function ($result) use ($matcher) {
                    [$matched, $rest] = $result;

                    return $matcher($rest)->map(function ($result) use ($matched) {
                        [$matched2, $rest2] = $result;

                        return [concatM($matched, fromValue($matched2)), $rest2];
                    });
                })
                : ($b ? $b : $matcher($a)->bind(maybeMapFirst(fromValue)));
        }, null, $matchers);

        return $result instanceof Maybe
            ? bind(maybeMapFirst($map), $result)
            : nothing();
    })(...func_get_args());
}

// oneof' :: ([([a] -> Maybe b)] -> [a] -> Maybe b
function oneOfP(Listt $matchers, Listt $a = null)
{
    return curryN(2, function (Listt $matchers, Listt $a) {
        $result = reduce(function (?Maybe $b, callable $matcher) use ($a) {
            return $b instanceof Just
                ? $b
                : $matcher($a);
        }, null, $matchers);

        return $result instanceof Maybe
            ? $result
            : nothing();
    })(...func_get_args());
}

// lazyP :: ([a] -> Maybe b) -> [a] -> Maybe [b]
function lazyP(callable $fn, Listt $a = null)
{
    return curryN(2, function (callable $fn, Listt $a) {
        return $fn($a);
    })(...func_get_args());
}

// denest :: ([a] -> (b, [a]))) -> ([a] -> Maybe(b, [a])))
function denest(callable $matcher)
{
    $map = [];

    return function (Listt $a) use ($matcher, &$map) {
        $key = spl_object_id($a);
        if (isset($map[$key])) {
            return nothing();
        }

        $map[$key] = true;

        return $matcher($a);
    };
}

function tokens(string $input) : Listt
{
    $tokens = preg_split('//', $input);
    $tokens = array_filter($tokens);
    $tokens = fromIterable($tokens);
    $tokens = $tokens->map(Stringg::of);

    return $tokens;
}

class ParserTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Grammar
     *
     * Expr = IntVal a
     *      | Sum Expr Expr
     *      | Mul Expr Expr
     *      | Sqr Expr
     *
     * Token = Num a
     *      | Op a
     *      | ParenthesisOpen
     *      | ParenthesisClose
     *
     *   (1 + 2)        === Sum(IntVal(1), IntVal(2))
     *   1 + (2 + 3)    === Sum(IntVal(1), Sum(IntVal(2), IntVal(3))
     *   1 + 3^2        === Sum(IntVal(1), Sqr(3))
     *
     */
    public function test_generated_ast()
    {
        $hf = function (callable $fn, Listt $l = null) {
            return curryN(2, function (callable $fn, Listt $l) {
                return $fn(reduce(concatM, Stringg::mempty(), $l));
            })(...func_get_args());
        };

        $literal = tokenizeP(numbersP, $hf(function (Stringg $a) {
            return ['int', $a->extract()];
        }));
        $opAdd = tokenizeP(charP('+'), $hf(function (Stringg $a) {
            return ['add', $a->extract()];
        }));
        $opMul = tokenizeP(charP('*'), $hf(function (Stringg $a) {
            return ['mul', $a->extract()];
        }));
        $parOp = tokenizeP(charP('('), $hf(function (Stringg $a) {
            return ['po', $a->extract()];
        }));
        $parCl = tokenizeP(charP(')'), $hf(function (Stringg $a) {
            return ['pc', $a->extract()];
        }));

        $operator = oneOfP(fromIterable([
            $opAdd, $opMul
        ]));

        $binary = denest(allOfP(fromIterable([
            &$expression, $operator, &$expression
        ]), function (Listt $attr) {
            return ['bin', $attr->extract()];
        }));

        $grouping = allOfP(fromIterable([
            $parOp, &$expression, $parCl,
        ]), function (Listt $attr) {
            return ['group', $attr->extract()[1]];
        });

        $expression = oneOfP(fromIterable([
            $binary,
            $grouping,
            $literal,
        ]));

        $tokens = tokens('2+(1+223)*(6+1)');

        $result = $expression($tokens);
        $result = $result->extract()[0];
        $this->assertEquals([
            'bin',
            [
                ['int', 2],
                ['add', '+'],
                [
                    'bin',
                    [
                        [
                            'group',
                            [
                                'bin',
                                [
                                    ['int', 1],
                                    ['add', '+'],
                                    ['int', 223],
                                ],
                            ],
                        ],
                        ['mul', '*'],
                        [
                            'group',
                            [
                                'bin',
                                [
                                    ['int', 6],
                                    ['add', '+'],
                                    ['int', 1],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    public function test_integration_with_free_calc()
    {
        $hf = function (callable $fn, Listt $l = null) {
            return curryN(2, function (callable $fn, Listt $l) {
                return $fn(reduce(concatM, Stringg::mempty(), $l));
            })(...func_get_args());
        };

        $literal = tokenizeP(numbersP, $hf(function (Stringg $a) {
            return int((int) $a->extract());
        }));
        $opAdd = tokenizeP(charP('+'), $hf(function (Stringg $a) {
            return sum;
        }));
        $opMul = tokenizeP(charP('*'), $hf(function (Stringg $a) {
            return mul;
        }));
        $parOp = tokenizeP(charP('('), $hf(function (Stringg $a) {
            return $a->extract();
        }));
        $parCl = tokenizeP(charP(')'), $hf(function (Stringg $a) {
            return $a->extract();
        }));

        $operator = oneOfP(fromIterable([
            $opAdd, $opMul
        ]));

        $binary = denest(allOfP(fromIterable([
            &$expression, $operator, &$expression
        ]), function (Listt $attr) {
            [$a, $op, $b] =  $attr->extract();

            return $op($a, $b);
        }));

        $grouping = allOfP(fromIterable([
            $parOp, &$expression, $parCl,
        ]), function (Listt $attr) {
            return $attr->extract()[1];
        });

        $expression = oneOfP(fromIterable([
            $binary,
            $grouping,
            $literal,
        ]));

        $tokens = tokens('2+(1+223)*(6+1)');

        $result = $expression($tokens);
        $calc = $result->extract()[0];

        $expected = '(2+((1+223)*(6+1)))';

        $result = foldFree(interpretPrint, $calc, Identity::of);
        $this->assertEquals(
            Identity::of(Stringg::of($expected)),
            $result
        );
    }
}
