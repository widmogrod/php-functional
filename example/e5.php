<?php
require_once 'vendor/autoload.php';

$collectionA = Applicative\Collection::create([
    function($a) {
        return 3 + $a;
    },
    function($a) {
        return 4 + $a;
    },
]);
$collectionB = Applicative\Collection::create([
    4, 5, 6
]);

$collectionC = $collectionA->ap($collectionB);

var_dump($collectionC);


