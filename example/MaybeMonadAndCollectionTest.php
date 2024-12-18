<?php

declare(strict_types=1);

namespace example;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Widmogrod\Functional as f;
use Widmogrod\Monad\Maybe;
use function Widmogrod\Monad\Maybe\just;
use function Widmogrod\Monad\Maybe\nothing;
use const Widmogrod\Monad\Maybe\maybeNull;

class MaybeMonadAndCollectionTest extends TestCase
{
    #[DataProvider('provideData')]
    public function test_it_should_extract_elements_which_exists($data)
    {
        // $get :: String a -> [b] -> Maybe b
        $get = f\curryN(2, function ($key, $array) {
            return isset($array[$key])
                ? just($array[$key])
                : nothing();
        });

        $listOfFirstImages = f\pipeline(
            f\fromValue,
            f\map(maybeNull),
            f\map(f\bind($get('meta'))),
            f\map(f\bind($get('images'))),
            f\map(f\bind($get(0)))
        );

        $result = $listOfFirstImages($data);

        $this->assertEquals(
            f\fromIterable([just('//first.jpg'), just('//third.jpg'), nothing()]),
            $result
        );
    }

    #[DataProvider('provideData')]
    public function test_it_should_extract_elements_which_exists_alternative_solution($data)
    {
        // $get :: String a -> Maybe [b] -> Maybe b
        $get = function ($key) {
            return f\bind(function ($array) use ($key) {
                return isset($array[$key])
                    ? just($array[$key])
                    : nothing();
            });
        };

        $result = f\fromIterable($data)
            ->map(Maybe\maybeNull)
            ->map($get('meta'))
            ->map($get('images'))
            ->map($get(0));

        $this->assertEquals(
            f\fromIterable([just('//first.jpg'), just('//third.jpg'), nothing()]),
            $result
        );
    }

    public static function provideData()
    {
        return [
            'default' => [
                [
                    ['id' => 1, 'meta' => ['images' => ['//first.jpg', '//second.jpg']]],
                    ['id' => 2, 'meta' => ['images' => ['//third.jpg']]],
                    ['id' => 3],
                ]
            ],
        ];
    }
}
