<?php
namespace example;

use Widmogrod\Functional as f;
use Widmogrod\Primitive\Listt;

class ApplicativeFunctorTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_apply_every_function_in_collection_with_every_item_in_second()
    {
        $collectionA = Listt::of([
            function ($a) {
                return 3 + $a;
            },
            function ($a) {
                return 4 + $a;
            },
        ]);

        $collectionB = Listt::of([
            1,
            2
        ]);

        $result = $collectionA->ap($collectionB);

        $this->assertInstanceOf(Listt::class, $result);
        $this->assertEquals(Listt::of([4, 5, 5, 6]), $result);
    }
}



