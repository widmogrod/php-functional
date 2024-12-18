<?php

declare(strict_types=1);

namespace test\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;

class InvokeTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it($method, $input, $output)
    {
        $curried = f\invoke($method);
        $this->assertEquals($output, f\invoke($method, $input));
        $this->assertEquals($output, $curried($input));
    }

    public static function provideData()
    {
        return [
            'should return value from method' => ['getString', new InvokeTest2, 'this-is-my-string']
        ];
    }
}

class InvokeTest2
{
    public function getString()
    {
        return 'this-is-my-string';
    }
}
