<?php
namespace example;

use Functor;
use Functional as f;

class FunctorCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_return_new_map()
    {
        $collection = Functor\Collection::create([
            ['id' => 1, 'name' => 'One'],
            ['id' => 2, 'name' => 'Two'],
            ['id' => 3, 'name' => 'Three'],
        ]);

        $result = $collection->map(function ($a) {
            return $a['id'] + 1;
        });

        $this->assertEquals([2, 3, 4], f\valueOf($result));
    }
}

