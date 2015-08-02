<?php
namespace test\Functional;

use Functional as f;

class PipelineTest extends \PHPUnit_Framework_TestCase
{
    function test_it_should_retun_function_accepting_arguments()
    {
        $this->assertInstanceOf(\Closure::class, f\pipeline('strtolower', 'strtoupper'));
    }

    /**
     * @dataProvider provideData
     */
    public function test_it_should_be_curried(
        $functions,
        $value,
        $expected
    ) {
        /** @var callable $fn */
        $fn = call_user_func_array('Functional\pipeline', $functions);
        $this->assertEquals(
            $expected,
            $fn($value)
        );
    }

    public function provideData()
    {
        return [
            'two function' => [
                '$functions' => ['strtolower', 'strtoupper'],
                '$value' => 'aBcD',
                '$expected' => 'ABCD'
            ],
        ];
    }
}
