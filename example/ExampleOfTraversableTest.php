<?php

namespace example;

use Widmogrod\Primitive\Listt;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe as m;

function value_is($x)
{
    return $x % 2 == 1 ? m\nothing() : m\just($x);
}

class ExampleOfTraversableTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_traverse_just()
    {
        $list = Listt::of([2, 4]);
        $result = f\traverse('example\value_is', $list);

        $this->assertEquals(m\just([2, 4]), $result);
    }

    public function test_it_traverse_nothing()
    {
        $list = Listt::of([1, 2, 3, 4]);
        $result = f\traverse('example\value_is', $list);

        $this->assertEquals(m\nothing(), $result);
    }
}
