<?php
namespace test\Functional;

use Functional as f;

class FlipTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFunctions
     */
    public function test_it_should_flip_func_arguments(
        callable $func,
        array $args,
        $expected
    ) {
        $flipped = f\flip($func);

        $this->assertEquals(
            $expected,
            call_user_func_array($flipped, $args)
        );
    }

    public function provideFunctions()
    {
        return [
            'two arguments' => [
                '$func' => function ($a, $b) {
                    return [$a, $b];
                },
                '$args' => [1, 2],
                '$expected' => [2, 1]
            ],
            'three arguments' => [
                '$func' => function ($a, $b, $c) {
                    return [$a, $b, $c];
                },
                '$args' => [1, 2, 3],
                '$expected' => [2, 1, 3]
            ],
        ];
    }

    /**
     * @dataProvider provideFunctions
     */
    public function test_it_should_curry_if_not_enough_args_passed(
        callable $func,
        array $args,
        $expected
    ) {
        $flipped = f\flip($func);
        $x = f\head($args);
        $xs = f\tail($args);

        $curried = $flipped($x);

        $this->assertEquals(
            $expected,
            call_user_func_array($curried, $xs)
        );
    }
}
