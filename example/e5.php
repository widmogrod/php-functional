<?php
require_once 'vendor/autoload.php';

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
assert(f\valueOf($result) === [4, 5, 5, 6]);

