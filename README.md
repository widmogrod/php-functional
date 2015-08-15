# PHP Monad [![Build Status](https://travis-ci.org/widmogrod/php-functional.svg)](https://travis-ci.org/widmogrod/php-functional)
## Introduction

Functional programing is a fascinating concept.
The purpose of this library is to explore `Functors`, `Applicative Functors` and `Monads` in OOP PHP, and provide examples of real world use case.

## Installation

```
composer require widmogrod/php-functional
```

## Development

This repository follows [semantic versioning concept](http://semver.org/). 
If you want to contribute, just follow [GitHub workflow](https://guides.github.com/introduction/flow/) and open a pull request. 

More information about changes you can find in [change log](/CHANGELOG.md)

## Testing

Quality assurance is brought to you by [PHPSpec](http://www.phpspec.net/)

```
composer test
```

## Use Cases
You can find more use cases and examples in the `example` directory.

### List Functor
``` php
use Functional as f;

$collection = Functor\Collection::create([
   ['id' => 1, 'name' => 'One'],
   ['id' => 2, 'name' => 'Two'],
   ['id' => 3, 'name' => 'Three'],
]);

$result = $collection->map(function($a) {
    return $a['id'] + 1;
});

assert(f\extract($result) === [2, 3, 4]);
```

### List Applicative Functor
Apply function on list of values and as a result, receive list of all possible combinations 
of applying function from the left list to a value in the right one.

``` haskel
[(+3),(+4)] <*> [1, 2] == [4, 5, 5, 6]
```

``` php
use Functional as f;

$collectionA = Applicative\Collection::create([
    function($a) {
        return 3 + $a;
    },
    function($a) {
        return 4 + $a;
    },
]);
$collectionB = Applicative\Collection::create([
    1, 2
]);

$result = $collectionA->ap($collectionB);

assert($result instanceof Applicative\Collection);
assert(f\extract($result) === [4, 5, 5, 6]);
```

### Applicative Validation
When validating input values, sometimes it's better to collect information of all possible failures 
than breaking the chain on the first failure like in Either Monad.


``` php
use Functional as f;
use Applicative\Validator;

function isPasswordLongEnough($password)
{
    return strlen($password) > 6
        ? Validator\Success::create($password)
        : Validator\Failure::create(
            'Password must have more than 6 characters'
        );
}

function isPasswordStrongEnough($password)
{
    return preg_match('/[\W]/', $password)
        ? Validator\Success::create($password)
        : Validator\Failure::create([
            'Password must contain special characters'
        ]);
}

function isPasswordValid($password)
{
    return Validator\Success::create(Functional\curryN(2, function () use ($password) {
        return $password;
    }))
        ->ap(isPasswordLongEnough($password))
        ->ap(isPasswordStrongEnough($password));
}

$resultA = isPasswordValid("foo");
assert($resultA instanceof Applicative\Validator\Failure);
assert(f\extract($resultA) === [
    'Password must have more than 6 characters',
    'Password must contain special characters',
]);

$resultB = isPasswordValid("asdqMf67123!oo");
assert($resultB instanceof Applicative\Validator\Success);
assert(f\extract($resultB) === 'asdqMf67123!oo');
```

### Maybe and List Monad
Extracting from a list of uneven values can be tricky and produce nasty code full of `if (isset)` statements.
By combining List and Maybe Monad, this process becomes simpler and more readable.

``` php
use Monad\Maybe;
use Monad\Collection;

$data = [
    ['id' => 1, 'meta' => ['images' => ['//first.jpg', '//second.jpg']]],
    ['id' => 2, 'meta' => ['images' => ['//third.jpg']]],
    ['id' => 3],
];

$get = function ($key) {
    return function ($array) use ($key) {
        return isset($array[$key])
            ? Maybe\just($array[$key])
            : Maybe\nothing();
    };
};

$listOfFirstImages = Collection::create($data)
    ->bind($get('meta'))
    ->bind($get('images'))
    ->bind($get(0))
    ->extract();

assert($listOfFirstImages->extract() === ['//first.jpg', '//third.jpg', null]);
```

### Either Monad
In `php` world, the most popular way of saying that something went wrong is to throw an exception.
This results in nasty `try catch` blocks and many of if statements.
Either Monad shows how we can fail gracefully without breaking the execution chain and making the code more readable.
The following example demonstrates combining the contents of two files into one. If one of those files does not exist the operation fails gracefully.

``` php
use Functional as f;
use Monad\Either;

function read($file)
{
    return is_file($file)
        ? Either\Right::create(file_get_contents($file))
        : Either\Left::create(sprintf('File "%s" does not exists', $file));
}

$concat = f\liftM2(
    read(__DIR__ . '/e1.php'),
    read('aaa'),
    function ($first, $second) {
        return $first . $second;
    }
);

assert($concat instanceof Either\Left);
assert($concat->extract() === 'File "aaa" does not exists');
```

## Credits & Beers
This library exists only thanks **great people** who share their knowledge about Monads, Functors, Applicatives.
Thank you:
 * [@egonSchiele](https://github.com/egonSchiele)
 * [@folktale](https://github.com/folktale)
 * [@robotlolita](https://github.com/robotlolita)
 * and more

If you going to visit Cracow (Poland), let me know - It's my treat!

Here links to their articles`/`libraries that help me understood the domain:
 * http://adit.io/posts/2013-04-17-functors,_applicatives,_and_monads_in_pictures.html
 * http://learnyouahaskell.com/functors-applicative-functors-and-monoids
 * http://learnyouahaskell.com/starting-out#im-a-list-comprehension
 * http://robotlolita.me/2013/12/08/a-monad-in-practicality-first-class-failures.html
 * http://robotlolita.me/2014/03/20/a-monad-in-practicality-controlling-time.html
 * https://github.com/folktale/data.either
