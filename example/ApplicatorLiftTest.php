<?php
namespace example;

use Monad;
use Functional as f;

function sum($a, $b)
{
    return $a + $b;
}

class ApplicatorLiftTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_sum_all_from_one_collection_with_elements_from_second()
    {
        $collectionA = Monad\Collection::of([1, 2]);
        $collectionB = Monad\Collection::of([4, 5]);

        // sum <*> [1, 2] <*> [4, 5]
        $result = f\liftA2('example\sum', $collectionA, $collectionB);
        $this->assertInstanceOf(Monad\Collection::class, $result);
        $this->assertEquals([5, 6, 6, 7], f\valueOf($result));
    }

    public function test_it_should_sum_all_from_one_collection_with_single_element()
    {
        $justA = Monad\Identity::of(1);
        $collectionB = Monad\Collection::of([4, 5]);

        // sum <*> Just 1 <*> [4, 5]
        $result = f\liftA2('example\sum', $justA, $collectionB);
        $this->assertInstanceOf(Monad\Collection::class, $result);
        $this->assertEquals([5, 6], f\valueOf($result));
    }

    public function test_it_should_sum_value_with_elements_from_collection()
    {
        $justA = Monad\Identity::of(1);
        $collectionB = Monad\Collection::of([4, 5]);

        // sum <*> Just 1 <*> [4, 5]
        $result = f\liftA2('example\sum', $collectionB, $justA);
        $this->assertInstanceOf(Monad\Identity::class, $result);
        $this->assertEquals([5, 6], f\valueOf($result));
    }
}
