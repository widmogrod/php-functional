<?php

declare(strict_types=1);

namespace test\Useful;

use Exception;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Widmogrod\Useful\PatternMatcher;
use Widmogrod\Useful\PatternNotMatchedError;
use function Widmogrod\Useful\matchPatterns;
use const Widmogrod\Functional\identity;
use const Widmogrod\Useful\any;

class MatchTest extends TestCase
{
    #[DataProvider('provideInvalidPatterns')]
    public function test_it_should_fail_on_not_matched_patterns(
        array $patterns,
              $value,
              $expectedMessage
    )
    {
        $this->expectException(PatternNotMatchedError::class);
        $this->expectExceptionMessage($expectedMessage);

        matchPatterns($patterns, $value);
    }

    public static function provideInvalidPatterns()
    {
        return [
            'Empty pattern list' => [
                [],
                random_int(-1000, 1000),
                'Cannot match "integer" type. List of patterns is empty.',
            ],
            'Value not in pattern list' => [
                [
                    self::class => identity,
                    "RandomString" => identity,
                ],
                random_int(-1000, 1000),
                'Cannot match "integer" type. Defined patterns are: "test\Useful\MatchTest", "RandomString"',
            ],
            'Value not in tuple pattern list' => [
                [
                    [[self::class, stdClass::class], identity],
                    [["RandomString"], identity],
                ],
                [random_int(-1000, 1000)],
                'Cannot match "array" type. Defined patterns are: "0", "1"',
            ],
        ];
    }

    #[DataProvider('providePatterns')]
    public function test_it_should_match_given_value(
        array $patterns,
              $value,
              $expected
    )
    {
        $result = matchPatterns($patterns, $value);
        $this->assertSame(
            $expected,
            $result
        );
    }

    public static function providePatterns()
    {
        $std = new stdClass();
        $e = new Exception();
        $m = new MyPatternMatcher(100, 123);

        return [
            'single pattern' => [
                [
                    stdClass::class => identity,
                ],
                $std,
                $std,
            ],
            'single pattern fallback to any' => [
                [
                    stdClass::class => identity,
                    any => identity,
                ],
                $e,
                $e,
            ],
            'many patterns' => [
                [
                    Exception::class => identity,
                    self::class => identity,
                    stdClass::class => identity,
                ],
                $std,
                $std,
            ],
            'tuple patterns' => [
                [
                    [[stdClass::class, stdClass::class], function () {
                        return func_get_args();
                    }],
                ],
                [$std, $std],
                [$std, $std],
            ],
            'tuple fallback to any patterns' => [
                [
                    [[stdClass::class, stdClass::class], function () {
                        return func_get_args();
                    }],
                    [[any, any], function () {
                        return ['any', func_get_args()];
                    }],
                ],
                [$std, $m],
                ['any', [$std, $m]],
            ],
            'value as a PatternMatcher patterns' => [
                [
                    Exception::class => identity,
                    self::class => identity,
                    stdClass::class => identity,
                    MyPatternMatcher::class => function ($a, $b) {
                        return $a + $b;
                    }
                ],
                new MyPatternMatcher(100, 123),
                223,
            ],
        ];
    }
}

class MyPatternMatcher implements PatternMatcher
{
    private $a;
    private $b;

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @inheritdoc
     */
    public function patternMatched(callable $fn)
    {
        return $fn($this->a, $this->b);
    }
}
