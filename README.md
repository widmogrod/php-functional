# PHP Monad [![Build Status](https://travis-ci.org/widmogrod/php-monads.svg)](https://travis-ci.org/widmogrod/php-monads)
## Introduction

Monads are fascinating concept. 
Purpose of this library is to explore them in OOP PHP world.

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

Extract list of first images from collection.

```
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

## TODO

- [x] Unit monad
- [x] Maybe monad
- [ ] List monad
- [ ] Promise monad
- [ ] Thread monad

