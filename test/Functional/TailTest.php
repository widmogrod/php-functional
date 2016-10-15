<?php
namespace test\Functional;

use Widmogrod\Functional as f;

class TailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_return_all_elements_exception_of_a_first(
        $list,
        $expected
    ) {
        $this->assertEquals(
            $expected,
            f\tail($list)
        );
    }

    public function provideData()
    {
        return [
            'simple list' => [
                '$list' => [1, 2, 3],
                '$expected' => [2, 3]
            ],
            'empty list' => [
                '$list' => [],
                '$expected' => null
            ],
        ];
    }
}
