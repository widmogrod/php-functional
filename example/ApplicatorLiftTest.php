<?php

namespace example;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

function sum($a, $b)
{
    return $a + $b;
}

class ApplicatorLiftTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_sum_all_from_one_list_with_elements_from_second()
    {
        $listA = Listt::of([1, 2]);
        $listB = Listt::of([4, 5]);

        // sum <*> [1, 2] <*> [4, 5]
        $result = f\liftA2('example\sum', $listA, $listB);
        $this->assertInstanceOf(Listt::class, $result);
        $this->assertEquals([5, 6, 6, 7], f\valueOf($result));
    }

    public function test_it_should_sum_all_from_one_list_with_single_element()
    {
        // sum <$> [1, 2] <*> [4, 5]
        $sum = f\curryN(2, 'example\sum');
        $a = Listt::of([1, 2]);
        $b = Listt::of([4, 5]);

        $result = f\map($sum, $a)->ap($b);
        $this->assertEquals(
            Listt::of([5, 6, 6, 7]),
            $result
        );
    }
}
