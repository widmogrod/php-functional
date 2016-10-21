<?php
namespace test\Primitive;

use Widmogrod\Functional as f;
use Widmogrod\Helpful\SetoidLaws;
use Widmogrod\Primitive\Num;

class NumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideSetoidLaws
     */
    public function test_it_should_obay_setoid_laws(
        $a,
        $b,
        $c
    ) {
        SetoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $a, $b, $c
        );
    }

    private function randomize()
    {
        return Num::of(rand(-100000000, 100000000));
    }

    public function provideSetoidLaws()
    {
        return array_map(function () {
            return [
                $this->randomize(),
                $this->randomize(),
                $this->randomize(),
            ];
        }, array_fill(0, 50, null));
    }
}
