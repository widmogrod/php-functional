<?php

declare(strict_types=1);

namespace test\Functional;

use Widmogrod\Functional as f;

class InvokeTest extends \PHPUnit\Framework\TestCase
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
