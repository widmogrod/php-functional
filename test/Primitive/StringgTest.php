<?php

namespace test\Widmogrod\Primitive;

use Widmogrod\FantasyLand\Monoid;
use Widmogrod\Helpful\MonoidLaws;
use Widmogrod\Primitive\Stringg;
use Widmogrod\Functional as f;

class StringgTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideRandomizedData
     */
    public function test_it_should_obey_monoid_laws(Monoid $x, Monoid $y, Monoid $z)
    {
        MonoidLaws::test(
            f\curryN(3, [$this, 'assertEquals']),
            $x, $y, $z
        );
    }

    private function randomize()
    {
        return Stringg::of(md5(rand(0, 100)));
    }

    public function provideRandomizedData()
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
