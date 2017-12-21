<?php

declare(strict_types=1);

namespace example;

use function Widmogrod\Functional\fromIterable;

class FunctorCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function test_it_should_return_new_map()
    {
        $collection = fromIterable([
            ['id' => 1, 'name' => 'One'],
            ['id' => 2, 'name' => 'Two'],
            ['id' => 3, 'name' => 'Three'],
        ]);

        $result = $collection->map(function ($a) {
            return $a['id'] + 1;
        });

        $this->assertEquals(
            fromIterable([2, 3, 4]),
            $result
        );
    }
}
