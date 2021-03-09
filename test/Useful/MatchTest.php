<?php

declare(strict_types=1);

namespace test\Useful;

use Widmogrod\Useful\PatternMatcher;
use Widmogrod\Useful\PatternNotMatchedError;
use function Widmogrod\Useful\matchPatterns;
use const Widmogrod\Functional\identity;
use const Widmogrod\Useful\any;

class MatchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider provideInvalidPatterns
     */
    public function test_it_should_fail_on_not_matched_patterns(
        array $patterns,
        $value,
        $expectedMessage
    ) {
        $this->expectException(PatternNotMatchedError::class);
        $this->expectExceptionMessage($expectedMessage);

        matchPatterns($patterns, $value);
    }

    public function provideInvalidPatterns()
    {
        return [
            'Empty pattern list' => [
                '$patterns' => [],
                '$value' => random_int(-1000, 1000),
                '$expectedMessage' => 'Cannot match "integer" type. List of patterns is empty.',
            ],
            'Value not in pattern list' => [
                '$patterns' => [
                    self::class => identity,
                    "RandomString" => identity,
                ],
                '$value' => random_int(-1000, 1000),
                '$expectedMessage' => 'Cannot match "integer" type. Defined patterns are: "test\Useful\MatchTest", "RandomString"',
            ],
            'Value not in tuple pattern list' => [
                '$patterns' => [
                    [[self::class, \stdClass::class], identity],
                    [["RandomString"], identity],
                ],
                '$value' => [random_int(-1000, 1000)],
                '$expectedMessage' => 'Cannot match "array" type. Defined patterns are: "0", "1"',
            ],
        ];
    }

    /**
     * @dataProvider providePatterns
     */
    public function test_it_should_match_given_value(
        array $patterns,
        $value,
        $expected
    ) {
        $result = matchPatterns($patterns, $value);
        $this->assertSame(
            $expected,
            $result
        );
    }

    public function providePatterns()
    {
        $std = new \stdClass();
        $e = new \Exception();
        $m = new MyPatternMatcher(100, 123);

        return [
            'single pattern' => [
                '$patterns' => [
                    \stdClass::class => identity,
                ],
                '$value' => $std,
                '$expected' => $std,
            ],
            'single pattern fallback to any' => [
                '$patterns' => [
                    \stdClass::class => identity,
                    any => identity,
                ],
                '$value' => $e,
                '$expected' => $e,
            ],
            'many patterns' => [
                '$patterns' => [
                    \Exception::class => identity,
                    self::class => identity,
                    \stdClass::class => identity,
                ],
                '$value' => $std,
                '$expected' => $std,
            ],
            'tuple patterns' => [
                '$patterns' => [
                    [[\stdClass::class, \stdClass::class], function () {
                        return func_get_args();
                    }],
                ],
                '$value' => [$std, $std],
                '$expected' => [$std, $std],
            ],
            'tuple fallback to any patterns' => [
                '$patterns' => [
                    [[\stdClass::class, \stdClass::class], function () {
                        return func_get_args();
                    }],
                    [[any, any], function () {
                        return ['any', func_get_args()];
                    }],
                ],
                '$value' => [$std, $m],
                '$expected' => ['any', [$std, $m]],
            ],
            'value as a PatternMatcher patterns' => [
                '$patterns' => [
                    \Exception::class => identity,
                    self::class => identity,
                    \stdClass::class => identity,
                    MyPatternMatcher::class => function ($a, $b) {
                        return $a + $b;
                    }
                ],
                '$value' => new MyPatternMatcher(100, 123),
                '$expected' => 223,
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
