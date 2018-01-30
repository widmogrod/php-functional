<?php

declare(strict_types=1);

namespace example;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

function sum_($a, $b)
{
    return $a + $b;
}

class ApplicatorLiftTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_sum_all_from_one_list_with_elements_from_second()
    {
        $listA = f\fromIterable([1, 2]);
        $listB = f\fromIterable([4, 5]);

        // sum <*> [1, 2] <*> [4, 5]
        $result = f\liftA2('example\sum_', $listA, $listB);
        $this->assertInstanceOf(Listt::class, $result);
        $this->assertEquals([5, 6, 6, 7], f\valueOf($result));
    }

    public function test_it_should_sum_all_from_one_list_with_single_element()
    {
        // sum <$> [1, 2] <*> [4, 5]
        $sum = f\curryN(2, 'example\sum_');
        $a = f\fromIterable([1, 2]);
        $b = f\fromIterable([4, 5]);

        $result = f\map($sum, $a)->ap($b);
        $this->assertEquals(
            f\fromIterable([5, 6, 6, 7]),
            $result
        );
    }
}
