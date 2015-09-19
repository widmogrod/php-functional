<?php
namespace example;

use Monad\Maybe;
use Monad\Collection;
use Functional as f;

class MaybeMonadAndCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideData
     */
    public function test_it_should_extract_elements_which_exists($data)
    {
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

    /**
     * @dataProvider provideData
     */
    public function test_it_should_extract_elements_which_exists_alternative_solution($data)
    {
        // $get :: String a -> Maybe [b] -> Maybe b
        $get = function ($key) {
            return f\bind(function ($array) use ($key) {
                return isset($array[$key])
                    ? Maybe\just($array[$key])
                    : Maybe\nothing();
            });
        };

        $result = Collection::of($data)
            ->map(Maybe\maybeNull)
            ->bind($get('meta'))
            ->bind($get('images'))
            ->bind($get(0));

        $result = f\valueOf($result);

        $this->assertEquals(
            ['//first.jpg', '//third.jpg', null],
            $result
        );
    }

    public function provideData()
    {
        return [
            'default' => [
                '$data' => [
                    ['id' => 1, 'meta' => ['images' => ['//first.jpg', '//second.jpg']]],
                    ['id' => 2, 'meta' => ['images' => ['//third.jpg']]],
                    ['id' => 3],
                ]
            ],
        ];
    }
}
