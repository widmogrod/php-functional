<?php

namespace test\Useful;

use Widmogrod\Useful\PatternMatcher;
use Widmogrod\Useful\PatternNotMatchedError;
use const Widmogrod\Functional\identity;
use function Widmogrod\Useful\match;

class MatchTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideInvalidPatterns
     */
    public function test_it_should_fail_on_not_matched_patterns(
        array $patterns,
        $value,
        $expectedMessage
    ) {
        $this->setExpectedException(PatternNotMatchedError::class, $expectedMessage);

        match($patterns, $value);
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
        $result = match($patterns, $value);
        $this->assertSame(
            $expected,
            $result
        );
    }

    public function providePatterns()
    {
        $std = new \stdClass();

        return [
            'single pattern' => [
                '$patterns' => [
                    \stdClass::class => identity,
                ],
                '$value' => $std,
                '$expected' => $std,
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
