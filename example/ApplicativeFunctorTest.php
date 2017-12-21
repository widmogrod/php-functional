<?php

namespace example;

use function Widmogrod\Functional\fromIterable;
use Widmogrod\Primitive\Listt;

class ApplicativeFunctorTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_apply_every_function_in_collection_with_every_item_in_second()
    {
        $collectionA = fromIterable([
            function ($a) {
                return 3 + $a;
            },
            function ($a) {
                return 4 + $a;
            },
        ]);

        $collectionB = fromIterable([
            1,
            2
        ]);

        $result = $collectionA->ap($collectionB);

        $this->assertInstanceOf(Listt::class, $result);
        $this->assertEquals(fromIterable([4, 5, 5, 6]), $result);
    }
}
