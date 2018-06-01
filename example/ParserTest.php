<?php

declare(strict_types=1);

namespace example;

use Widmogrod\Monad\Identity;
use Widmogrod\Monad\Maybe\Just;
use Widmogrod\Monad\Maybe\Maybe;
use Widmogrod\Primitive\EmptyListError;
use Widmogrod\Primitive\Listt;
use Widmogrod\Primitive\Stringg;
use function Widmogrod\Functional\append;
use function Widmogrod\Functional\bind;
use function Widmogrod\Functional\concatM;
use function Widmogrod\Functional\curryN;
use function Widmogrod\Functional\dropWhile;
use function Widmogrod\Functional\emptyM;
use function Widmogrod\Functional\equal;
use function Widmogrod\Functional\fromIterable;
use function Widmogrod\Functional\fromNil;
use function Widmogrod\Functional\fromValue;
use function Widmogrod\Functional\head;
use function Widmogrod\Functional\length;
use function Widmogrod\Functional\reduce;
use function Widmogrod\Functional\tail;
use function Widmogrod\Monad\Free\foldFree;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;
use const Widmogrod\Functional\fromValue;

// Some dependencies are needed
require_once __DIR__ . '/FreeCalculatorTest.php';
require_once __DIR__ . '/FreeUnionTypeGeneratorTest.php';


/**
 *      match      :: Monoid a, Semigroup a, Setoid a => (a -> Bool) -> [a] -> Maybe (a, [a])
 *      numbers :: [a] -> Maybe (a, [a])
 *      numbers = match isNumber
 *
 *      char :: a -> [a] -> Maybe (a, [a])
 *
 *      tokenize' :: ([a] -> Maybe (a, [a])) -> ([a] -> b) -> [a] -> Maybe (b, [a])
 *      allof' :: ([([a] -> Maybe (b, [a]))] -> ([b] -> b) -> [a] -> Maybe (b, [a])
 *      oneof' :: ([([a] -> Maybe (b, [a]))] -> [a] -> Maybe (b, [a])
 *
 *      reduce :: (a -> b -> a) a [b]
 *
 *      foldr :: (a -> b -> b) -> b -> t a -> b
 *      foldl :: (b -> a -> b) -> b -> t a -> b
 *
 *      literal  = tokenize numbers (\ys -> atoi(concat(ys)))
 *      operator = oneof' [tokenize (char "+") OpSum
 *                        ,tokenize (char "*") OpMul]
 *
 *      denest :: ([a] -> (b, [a]))) -> ([a] -> Maybe(b, [a])))
 *
 *      Stream s u m
 */

// match :: Monoid a, Semigroup a, Setoid a => (a -> a -> Bool) -> [a] -> Maybe (a, [a])
function matchP(callable $predicate, Listt $a = null)
{
    return curryN(2, function (callable $predicate, Listt $a) {
        try {
            $matched = emptyM(head($a));
            $rest = $a;
            do {
                try {
                    $x = head($rest);
                    $xs = tail($rest);
                } catch (EmptyListError $e) {
                    break;
                }

                $continue = $predicate($x, $matched);

                if ($continue) {
                    $matched = concatM($matched, $x);
                    $rest = $xs;
                }
            } while ($continue);

            return equal($matched, emptyM($matched))
                ? nothing()
                : just([$matched, $rest]);
        } catch (EmptyListError $e) {
            return nothing();
        }
    })(...func_get_args());
}

const numbersP = 'example\\numbersP';

// numbers :: [a] -> Maybe (a, [a])
function numbersP(Listt $a)
{
    return matchP(function (Stringg $s) {
        return \is_numeric($s->extract());
    }, $a);
}

// char :: Char -> [a] -> Maybe (a, [a])
function charP(string $char, Listt $a = null)
{
    return curryN(2, function (string $char, Listt $a) {
        try {
            $x = head($a);
            $xs = tail($a);

            return equal($x, Stringg::of($char))
                ? just([$x, $xs])
                : nothing();
        } catch (EmptyListError $e) {
            return nothing();
        }
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

// tokenize' :: ([a] -> Maybe (a, [a])) -> (a -> b) -> [a] -> Maybe (b, [a])
function tokenizeP(callable $matcher, callable $transform = null, Listt $a = null)
{
    return curryN(3, function (callable $matcher, callable $transform, Listt $a) {
        return bind(maybeMapFirst($transform), $matcher($a));
    })(...func_get_args());
}

// allof' :: ([([a] -> Maybe (b, [a]))] -> ([b] -> b) -> [a] -> Maybe (b, [a])
function allOfP(Listt $matchers, callable $transform = null, Listt $a = null)
{
    return curryN(3, function (Listt $matchers, callable $transform, Listt $a) {
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
            ? bind(maybeMapFirst($transform), $result)
            : nothing();
    })(...func_get_args());
}


// many' :: ([([a] -> Maybe (b, [a]))] -> ([b] -> b) -> [a] -> Maybe (b, [a])
// Zero or more.
function manyP(Listt $matchers, callable $transform = null, Listt $a = null)
{
    return curryN(3, function (Listt $matchers, callable $transform, Listt $a) {
        $res = fromNil();
        $m = oneOfP($matchers);

        do {
            $r = $m($a);
            if ($r instanceof Just) {
                [$mached, $rest] = $r->extract();
                // TODO this is also kind-a not optimal
                $res = append($res, fromValue($mached));
                $a = $rest;
            }
        } while ($r instanceof Just);

        $result = length($res) > 0
            ? just([$res, $a])
            : nothing();

        return $result instanceof Maybe
            ? bind(maybeMapFirst($transform), $result)
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

// endByP :: ([a] -> Maybe b) -> ([a] -> Maybe b) -> [a] -> Maybe [b]
function endByP(callable $matcher, callable $matcherEnd = null, callable $transform = null, Listt $a = null)
{
    return curryN(4, function (callable $matcher, callable $matcherEnd, callable $transform, Listt $a): Maybe {
        $before = fromNil();
        $resultEnd = nothing();
        $matched = false;
        try {
            do {
                $resultEnd = $matcherEnd($a);
                $matched = $resultEnd instanceof Just;
                if (!$matched) {
                    $before = append($before, fromValue(head($a)));
                    $a = tail($a);
                }
            } while (!$matched);
        } catch (EmptyListError $e) {
            // Jup, do nothing.
        }

        if (!$matched) {
            return nothing();
        }

        $result = $matcher($before);
        if ($result instanceof Just) {
            [$m, $rest] = $result->extract();
            if (length($rest)) {
                return nothing();
            }

            [$e, $restEnd] = $resultEnd->extract();

            return just([
                $transform(fromIterable([$m, $e])),
                $restEnd
            ]);
        }

        return nothing();
    })(...func_get_args());
}

function maybeP(callable $matcher, Listt $a = null)
{
    return curryN(2, function (callable $matcher, Listt $a) {
        $r = $matcher($a);

        return $r instanceof Just
            ? $r
            : just([[], $a]);
    })(...func_get_args());
}


// lazyP :: ([a] -> Maybe b) -> [a] -> Maybe [b]
function lazyP(callable $fn, Listt $a = null)
{
    return curryN(2, function (callable $fn, Listt $a) {
        return $fn($a);
    })(...func_get_args());
}

// denest :: ([a] -> (b, [a]))) -> ([a] -> Maybe (b, [a])))
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

function tokens(string $input): Listt
{
    $tokens = preg_split('//', $input);
    $tokens = array_filter($tokens);
    $tokens = fromIterable($tokens);
    $tokens = $tokens->map(Stringg::of);

    return $tokens;
}

class ParserTest extends \PHPUnit\Framework\TestCase
{
    public function test_generated_ast()
    {
        $literal = tokenizeP(numbersP, function (Stringg $a) {
            return ['int', $a->extract()];
        });
        $opAdd = tokenizeP(charP('+'), function (Stringg $a) {
            return ['add', $a->extract()];
        });
        $opMul = tokenizeP(charP('*'), function (Stringg $a) {
            return ['mul', $a->extract()];
        });
        $parOp = tokenizeP(charP('('), function (Stringg $a) {
            return ['po', $a->extract()];
        });
        $parCl = tokenizeP(charP(')'), function (Stringg $a) {
            return ['pc', $a->extract()];
        });

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
        $literal = tokenizeP(numbersP, function (Stringg $a) {
            return int((int) $a->extract());
        });
        $opAdd = tokenizeP(charP('+'), function (Stringg $a) {
            return sum;
        });
        $opMul = tokenizeP(charP('*'), function (Stringg $a) {
            return mul;
        });
        $parOp = tokenizeP(charP('('), function (Stringg $a) {
            return $a->extract();
        });
        $parCl = tokenizeP(charP(')'), function (Stringg $a) {
            return $a->extract();
        });

        $operator = oneOfP(fromIterable([
            $opAdd, $opMul
        ]));

        $binary = denest(allOfP(fromIterable([
            &$expression, $operator, &$expression
        ]), function (Listt $attr) {
            [$a, $op, $b] = $attr->extract();

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

    /**
     * data Maybe a = Just a | Nothing
     * data Either a b = Left a | Right b
     * data Free f a = Pure a | Free f (Free f a)
     *
     *
     * type UnionF _ next
     *  | Declare_ name [args] (a -> next)
     *  | Union_ a name [args] (a -> next)
     *
     * exp =
     * declaraton = "type" type "=" type "|"
     *
     * type   = word args
     * word   = char word
     * args   = word | word args
     */
    public function test_generate_data_types_as_array()
    {
        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexeme = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                // TODO Not optimal, for test only
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract()) === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexeme2 = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract(), " ") === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexemeOr = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract(), " \0\n\t\r|") === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        $reserved = function (string $name) {
            return matchP(function (Stringg $s, Stringg $matched) use ($name) {
                $c = concatM($matched, $s);
                $e = Stringg::of($name);
                if (equal($c, $e)) {
                    return true;
                }

                // TODO not optimal :/
                return preg_match(sprintf('/^%s(.+)/', preg_quote($c->extract())), $e->extract());
            });
        };

        $or = $lexeme(charP('|'));
        $eql = $lexeme(charP('='));
        $parOp = $lexeme(charP('('));
        $parCl = $lexeme(charP(')'));

        $upperCaseWord = $lexeme(matchP(function (Stringg $s, Stringg $matched) {
            return equal($matched, emptyM($matched))
                ? preg_match('/[A-Z]/', $s->extract())
                : preg_match('/[a-z]/i', $s->extract());
        }));
        $lowerCaseWord = $lexeme2(matchP(function (Stringg $s, Stringg $matched) {
            return strlen($matched->extract())
                ? false
                : preg_match('/[a-z]/', $s->extract());
        }));
        $reservedData = $lexeme($reserved('data'));
        $reservedDeriving = $lexeme($reserved('deriving'));

        $classDerivde = manyP(fromIterable([
            $upperCaseWord
        ]), function (Listt $a) {
            return ['deriveClass', $a->extract()];
        });

        $dataDeriving = allOfP(fromIterable([
            $reservedDeriving, $parOp, $classDerivde, $parCl,
        ]), function (Listt $l) {
            return ['deriving', $l->extract()[2]];
        });

        $grouping = allOfP(fromIterable([
            $parOp, &$typeName, $parCl,
        ]), function (Listt $attr) {
            return ['grp', $attr->extract()[1]];
        });

        $args = $lexeme2(manyP(fromIterable([
            oneofP(fromIterable([$lowerCaseWord, $grouping])),
        ]), function (Listt $attr) {
            return ['args', $attr->extract()];
        }));

        $typeNameWithoutArgs = $lexeme(allOfP(fromIterable([
            $upperCaseWord
        ]), function (Listt $a) {
            list($name) = $a->extract();

            return ['typeName', [$name, []]];
        }));

        $typeNameWithArgs = $lexeme(allOfP(fromIterable([
            $upperCaseWord, $args
        ]), function (Listt $a) {
            list($name, $args) = $a->extract();

            return ['typeName', [$name, $args]];
        }));

        $typeName = $lexemeOr(oneOfP(fromIterable([
            $typeNameWithArgs,
            $typeNameWithoutArgs
        ])));

        $representations = manyP(fromIterable([
            $typeName,
        ]), function (Listt $a) {
            return ['representation', $a->extract()];
        });

        $declaration = allOfP(fromIterable([
            $reservedData, $typeName, $eql, $representations,
        ]), function (Listt $a) {
            list(, $type, , $rep) = $a->extract();

            return ['declaration', [$type, $rep]];
        });

        $declarationDerived = endByP($declaration, $dataDeriving, function (Listt $a) {
            [$declaration, $derived] = $a->extract();

            return ['declaration-derived', [$declaration, $derived]];
        });

        $tokens = tokens('
        data A = B deriving (Show)
        data Maybe a = Just a | Nothing
        data Either a b = Left a | Right b
        data Free f a = Pure a | Free f (Free f a)
        ');
        $expression = manyP(fromIterable([
            $declarationDerived,
            $declaration,
        ]), function (Listt $a) {
            return ['types', $a->extract()];
        });

        $result = $expression($tokens);
        $ast = $result->extract()[0];

        $this->assertEquals(
            ["types", [
                ["declaration-derived", [
                    ["declaration", [
                        ["typeName", ["A", []]],
                        ["representation", [
                            ["typeName", ["B", []]]]]]],
                    ["deriving", ["deriveClass", ["Show"]]]]],
                ["declaration", [
                    ["typeName", ["Maybe", ["args", ["a"]]]],
                    ["representation", [
                        ["typeName", ["Just", ["args", ["a"]]]],
                        ["typeName", ["Nothing", []]]]]]],
                ["declaration", [
                    ["typeName", ["Either", ["args", ["a", "b"]]]],
                    ["representation", [
                        ["typeName", ["Left", ["args", ["a"]]]],
                        ["typeName", ["Right", ["args", ["b"]]]]]]]],
                ["declaration", [
                    ["typeName", ["Free", ["args", ["f", "a"]]]],
                    ["representation", [
                        ["typeName", ["Pure", ["args", ["a"]]]],
                        ["typeName", ["Free", ["args", ["f", ["grp", ["typeName", ["Free", ["args", ["f", "a"]]]]]]]]]]]]]
            ]],
            $ast
        );
    }

    public function buildParserForDataTypes()
    {
        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexeme = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                // TODO Not optimal, for test only
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract()) === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexeme2 = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract(), " ") === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        // lexeme :: ([a] -> Maybe (a, [a])) -> [a] -> Maybe (a, [a])
        $lexemeOr = function (callable $fn, Listt $a = null) {
            return curryN(2, function (callable $fn, Listt $a) {
                $trimNil = dropWhile(function (Stringg $s) {
                    return trim($s->extract(), " \0\n\t\r|") === "";
                }, $a);

                return $fn($trimNil);
            })(...func_get_args());
        };

        $reserved = function (string $name) {
            return matchP(function (Stringg $s, Stringg $matched) use ($name) {
                $c = concatM($matched, $s);
                $e = Stringg::of($name);
                if (equal($c, $e)) {
                    return true;
                }

                // TODO not optimal :/
                return preg_match(sprintf('/^%s(.+)/', preg_quote($c->extract())), $e->extract());
            });
        };

        $or = $lexeme(charP('|'));
        $eql = $lexeme(charP('='));
        $parOp = $lexeme(charP('('));
        $parCl = $lexeme(charP(')'));

        $upperCaseWord = $lexeme(matchP(function (Stringg $s, Stringg $matched) {
            return equal($matched, emptyM($matched))
                ? preg_match('/[A-Z]/', $s->extract())
                : preg_match('/[a-z]/i', $s->extract());
        }));
        $lowerCaseWord = $lexeme2(matchP(function (Stringg $s, Stringg $matched) {
            return strlen($matched->extract())
                ? false
                : preg_match('/[a-z]/', $s->extract());
        }));
        $reservedData = $lexeme($reserved('data'));
        $reservedDeriving = $lexeme($reserved('deriving'));

        $classDerivde = manyP(fromIterable([
            $upperCaseWord
        ]), function (Listt $a) {
            return derived($a->extract());
        });

        $dataDeriving = allOfP(fromIterable([
            $reservedDeriving, $parOp, $classDerivde, $parCl,
        ]), function (Listt $l) {
            return $l->extract()[2];
        });

        $grouping = allOfP(fromIterable([
            $parOp, &$typeName, $parCl,
        ]), function (Listt $attr) {
            return $attr->extract()[1];
        });

        $args = $lexeme2(manyP(fromIterable([
            oneofP(fromIterable([$lowerCaseWord, $grouping])),
        ]), function (Listt $attr) {
            return $attr->extract();
        }));

        $typeNameWithoutArgs = $lexeme(allOfP(fromIterable([
            $upperCaseWord
        ]), function (Listt $a) {
            list($name) = $a->extract();

            return [$name, []];
        }));

        $typeNameWithArgs = $lexeme(allOfP(fromIterable([
            $upperCaseWord, $args
        ]), function (Listt $a) {
            list($name, $args) = $a->extract();

            return [$name, $args];
        }));

        $typeName = $lexemeOr(oneOfP(fromIterable([
            $typeNameWithArgs,
            $typeNameWithoutArgs
        ])));

        $representations = manyP(fromIterable([
            $typeName,
        ]), function (Listt $a): Listt {
            return $a;
        });

        $declaration = allOfP(fromIterable([
            $reservedData, $typeName, $eql, $representations,
        ]), function (Listt $a) {
            list(, list($tname, $targ), , $rep) = $a->extract();

            return declaree(data_($tname, $targ), fromIterable($rep)->map(function ($t) {
                list($tname, $targ) = $t;

                return type($tname, $targ);
            }));
        });

        $declarationDerived = endByP($declaration, $dataDeriving, function (Listt $a) {
            [$declaration, $derived] = $a->extract();

            return declaree($declaration, fromValue($derived));
        });


        $expression = manyP(fromIterable([
            $declarationDerived,
            $declaration,
        ]), function (Listt $a) {
            return $a->extract();
        });

        return $expression;
    }

    /**
     * @dataProvider provideGeneratedCode
     */
    public function test_generate_data_types_as_free_string(string $input, string $expectedFileContents)
    {
        $tokens = tokens($input);

        $expression = $this->buildParserForDataTypes();
        $result = $expression($tokens);
        $ast = $result->extract()[0][0];

        $expected = file_get_contents(sprintf(__DIR__ . '/_Parser_assets/%s', $expectedFileContents));

        $result = foldFree(interpretTypesAndGenerate, $ast, Identity::of);
        $generated = $result->extract()->generate();
        $this->assertEquals($expected, $generated);
    }

    public function provideGeneratedCode()
    {
        return [
            'data A = B deriving (Show)' => [
                '$declaration' => 'data A = B deriving (Show)',
                '$toImplementation' => 'A.txt',
            ],
            'data Maybe a = Just a | Nothing' => [
                '$declaration' => 'data Maybe a = Just a | Nothing',
                '$toImplementation' => 'Maybe.txt',
            ],
            'data Either a b = Left a | Right b' => [
                '$declaration' => 'data Either a b = Left a | Right b',
                '$toImplementation' => 'Either.txt',
            ],
            'data FreeT f a = Pure a | Free f (FreeT f a)' => [
                '$declaration' => 'data FreeT f a = Pure a | Free f (FreeT f a)',
                '$toImplementation' => 'FreeT.txt',
            ],
        ];
    }
}
