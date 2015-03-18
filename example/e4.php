<?php
require_once 'vendor/autoload.php';

use Functional as f;

$justA       = Applicative\Just::create(1);
$collectionA = Applicative\Collection::create([1, 2]);
$collectionB = Applicative\Collection::create([4, 5]);

$plus = function($a, $b) {
    return $a + $b;
};

// $plus <*> [1, 2] <*> [4, 5]
$resultA = \Functional\liftA2($collectionA, $collectionB, $plus);
assert($resultA instanceof Applicative\Collection);
assert(f\valueOf($resultA) === [5, 6, 6, 7]);

// $plus <*> Just 1 <*> [4, 5]
$resultB = \Functional\liftA2($justA, $collectionB, $plus);
assert($resultB instanceof Applicative\Collection);
assert(f\valueOf($resultB) === [5, 6]);

// $plus <*> [1, 2] <*> Just 1
$resultC = \Functional\liftA2($collectionA, $justA, $plus);
assert($resultC instanceof Applicative\Just);
assert(f\valueOf($resultC) === [2, 3]);

