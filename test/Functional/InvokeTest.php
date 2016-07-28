<?php
namespace test\Functional;

use Functional as f;

class InvokeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it($method, $input, $output)
    {
        $curried = f\invoke($method);
        $this->assertEquals($output, f\invoke($method, $input));
        $this->assertEquals($output, $curried($input));
    }

    public function provideData()
    {
        return [
            'should return value from method' => ['getString', $this, 'this-is-my-string']
        ];
    }

    public function getString()
    {
        return 'this-is-my-string';
    }
}
