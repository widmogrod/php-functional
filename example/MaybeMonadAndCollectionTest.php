<?php
namespace example;

use Monad\Maybe;
use Monad\Collection;
use Functional as f;

class MaybeMonadAndCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_extract_elements_which_exists()
    {
        $data = [
            ['id' => 1, 'meta' => ['images' => ['//first.jpg', '//second.jpg']]],
            ['id' => 2, 'meta' => ['images' => ['//third.jpg']]],
            ['id' => 3],
        ];

        $get = function ($key) {
            return function (array $array) use ($key) {
                return isset($array[$key]) ? $array[$key] : null;
            };
        };

        $listOfFirstImages = Collection::create($data)
            ->lift(Maybe::create)
            ->lift($get('meta'))
            ->lift($get('images'))
            ->lift($get(0))
            ->valueOf();

        $this->assertEquals(['//first.jpg', '//third.jpg', null], $listOfFirstImages);
    }
}
