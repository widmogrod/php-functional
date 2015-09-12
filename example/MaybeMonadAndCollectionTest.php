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

        // $get :: String a -> [b] -> Maybe b
        $get = f\curryN(2, function ($key, $array) {
            return isset($array[$key])
                ? Maybe\just($array[$key])
                : Maybe\nothing();
        });

        $listOfFirstImages = f\pipeline(
            Collection::of
            , f\map(Maybe\maybeNull)
            , f\bind(f\bind($get('meta')))
            , f\bind(f\bind($get('images')))
            , f\bind(f\bind($get(0)))
            , f\join
        );

        $result = $listOfFirstImages($data);
        $result = f\valueOf($result);

        $this->assertEquals(
            ['//first.jpg', '//third.jpg', null],
            $result
        );
    }
}
