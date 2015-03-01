# PHP Monad [![Build Status](https://travis-ci.org/widmogrod/php-monads.svg)](https://travis-ci.org/widmogrod/php-monads)
## Introduction

Monads are fascinating concept. 
Purpose of this library is to explore monads in OOP PHP and provide few real world use cases.

## Installation

```
composer require widmogrod/php-monads
```

## Development

This repository fallows [semantic versioning concept](http://semver.org/). 
If you want to contribute, just fallow [GitHub workflow](https://guides.github.com/introduction/flow/) and open pull request. 

## Testing

Quality assurance is brought to you by [PHPSpec](http://www.phpspec.net/)

```
composer test
```

## Use Cases
### Maybe and List Monad

Extract list of first images from collection.

``` php
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
```

### Either Monad

Combine content of two files into one, but if one of those files does not exists fail gracefully.

``` php
use Monad\Either;
use Monad\Utils;

function read($file)
{
    return is_file($file)
        ? Either\Right::create(file_get_contents($file))
        : Either\Left::create(sprintf('File "%s" does not exists', $file));
}

$concat = Utils::liftM2(
    read(__FILE__),
    read('aaa'),
    function ($first, $second) {
        return $first . $second;
    }
);

assert($concat instanceof Either\Left);
assert($concat->orElse(Utils::returns) === 'File "aaa" does not exists');
```