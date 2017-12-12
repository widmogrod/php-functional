<?php

namespace example;

use Widmogrod\Monad\Maybe;
use Widmogrod\Monad\Maybe as m;
use Widmogrod\Primitive\Listt;
use Widmogrod\Functional as f;

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
                ? m\just($array[$key])
                : m\nothing();
        });

        $listOfFirstImages = f\pipeline(
            Listt::of,
            f\map(m\maybeNull),
            f\bind(f\bind($get('meta'))),
            f\bind(f\bind($get('images'))),
            f\bind(f\bind($get(0))),
            f\join
        );

        $result = $listOfFirstImages($data);

        $this->assertEquals(
            Listt::of([m\just('//first.jpg'), m\just('//third.jpg'), m\nothing()]),
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
                    ? m\just($array[$key])
                    : m\nothing();
            });
        };

        $result = Listt::of($data)
            ->map(Maybe\maybeNull)
            ->bind($get('meta'))
            ->bind($get('images'))
            ->bind($get(0));

        $this->assertEquals(
            Listt::of([m\just('//first.jpg'), m\just('//third.jpg'), m\nothing()]),
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
