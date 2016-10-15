<?php
namespace test\Functional;

use Widmogrod\Functional as f;

class CurryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideFunctionToCurry
     */
    public function test_it_should_detect_automatically_number_of_arguments_to_curry(
        $fn,
        $resultAfterCurries
    ) {
        $times = 0;
        $curried = f\curry($fn);

        do {
            $curried = $curried(1);
            $times++;
            // Prevents from infinite loop
            $this->assertLessThanOrEqual($resultAfterCurries, $times, 'Curried to more times than expected');
        } while (is_callable($curried));

        $this->assertEquals(
            $resultAfterCurries,
            $times,
            'Function was curried wrong number of times'
        );
    }

    public function provideFunctionToCurry()
    {
        return [
            'args = 0' => [
                '$fn' => function () {},
                '$resultAfterCurries' => 1,
            ],
            'args = 1' => [
                '$fn' => function ($a) {},
                '$resultAfterCurries' => 1,
            ],
            'args = 2' => [
                '$fn' => function ($a, $b) {},
                '$resultAfterCurries' => 2,
            ],
            'args = 2 with default' => [
                '$fn' => function ($a, $b = null) {},
                '$resultAfterCurries' => 2,
            ],
        ];
    }

    /**
     * @dataProvider provideFunctionsToCurry
     */
    public function test_it_curry_with_lest_arguments_if_defaults_are_provided(
        $result,
        $function
    ) {
        $this->assertEquals(
            $result,
            $function()
        );
    }

    public function provideFunctionsToCurry()
    {
        return [
            'curry args = 0 and default = 0' => [
                '$result' => null,
                '$function' => f\curry(function() {}, [])
            ],
            'curry args = 1 and default = 1' => [
                '$result' => [1],
                '$function' => f\curry(function($a) { return [$a]; }, [1])
            ],
            'curry args = 2 and default = 2' => [
                '$result' => [1, 2],
                '$function' => f\curry(function($a, $b) { return [$a, $b]; }, [1, 2])
            ],
            'curry args = 2 and default = 3' => [
                '$result' => [1, 2, 3],
                '$function' => f\curry(function($a) { return func_get_args(); }, [1, 2, 3])
            ],
            'curry args = 2 and default = 4' => [
                '$result' => [1, 2],
                '$function' => f\curry(function($a, $b) { return [$a, $b]; }, [1, 2, 3, 4])
            ],
        ];
    }
}
