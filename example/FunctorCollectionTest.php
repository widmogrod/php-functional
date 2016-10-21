<?php
namespace example;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

class MonadCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_return_new_map()
    {
        $collection = Listt::of([
            ['id' => 1, 'name' => 'One'],
            ['id' => 2, 'name' => 'Two'],
            ['id' => 3, 'name' => 'Three'],
        ]);

        $result = $collection->map(function ($a) {
            return $a['id'] + 1;
        });

        $this->assertEquals(
            Listt::of([2, 3, 4]),
            $result
        );
    }
}

