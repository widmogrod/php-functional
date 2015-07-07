<?php
namespace example;

use Applicative;
use Functional as f;

function sum($a, $b)
{
    return $a + $b;
}

class ApplicatorLiftTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_sum_all_from_one_collection_with_elements_from_second()
    {
        $collectionA = Applicative\Collection::create([1, 2]);
        $collectionB = Applicative\Collection::create([4, 5]);

        // sum <*> [1, 2] <*> [4, 5]
        $result = f\liftA2($collectionA, $collectionB, 'example\sum');
        $this->assertInstanceOf(Applicative\Collection::class, $result);
        $this->assertEquals([5, 6, 6, 7], f\valueOf($result));
    }

    public function test_it_should_sum_all_from_one_collection_with_single_element()
    {
        $justA = Applicative\Identity::create(1);
        $collectionB = Applicative\Collection::create([4, 5]);

        // sum <*> Just 1 <*> [4, 5]
        $result = f\liftA2($justA, $collectionB, 'example\sum');
        $this->assertInstanceOf(Applicative\Collection::class, $result);
        $this->assertEquals([5, 6], f\valueOf($result));
    }

    public function test_it_should_sum_value_with_elements_from_collection()
    {
        $justA = Applicative\Identity::create(1);
        $collectionB = Applicative\Collection::create([4, 5]);

        // sum <*> Just 1 <*> [4, 5]
        $result = f\liftA2($collectionB, $justA, 'example\sum');
        $this->assertInstanceOf(Applicative\Identity::class, $result);
        $this->assertEquals([5, 6], f\valueOf($result));
    }
}