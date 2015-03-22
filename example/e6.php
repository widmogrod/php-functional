<?php
require_once 'vendor/autoload.php';

use Functional as f;

// [1,2] >>= \n -> ['a','b'] >>= \ch -> return (n,ch) == [(1,'a'),(1,'b'),(2,'a'),(2,'b')]
$result = Monad\Collection::create([1, 2])->bind(function($n) {
    return Monad\Collection::create(['a', 'b'])
        ->bind(function($x) use ($n) {
            return [[$n, $x]];
        });
});

assert($result === [
    [1, 'a'],
    [1, 'b'],
    [2, 'a'],
    [2, 'b']
]);
