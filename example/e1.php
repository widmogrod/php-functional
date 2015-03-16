<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Monad\Maybe;
use Monad\Collection;

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

assert($listOfFirstImages === ['//first.jpg', '//third.jpg', null]);
