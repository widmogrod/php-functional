<?php
require_once 'vendor/autoload.php';

$justA = Applicative\Just::create(1);
$collectionA = Applicative\Collection::create([
    1, 2, 3
]);
$collectionB = Applicative\Collection::create([
    4, 5, 6
]);

$collectionC = \Functional\liftA2($collectionA, $collectionB, function($a, $b) {
    return $a + $b;
});
var_dump(\Functional\valueOf($collectionC), get_class($collectionC));

$collectionD = \Functional\liftA2($justA, $collectionB, function($a, $b) {
    return $a + $b;
});
var_dump(\Functional\valueOf($collectionD), get_class($collectionD));

$collectionE = \Functional\liftA2($collectionA, $justA, function($a, $b) {
    return $a + $b;
});

var_dump(\Functional\valueOf($collectionE), get_class($collectionE));


