<?php
require_once 'vendor/autoload.php';

$collection = Functor\Collection::create([
   ['id' => 1, 'name' => 'One'],
   ['id' => 2, 'name' => 'Two'],
   ['id' => 3, 'name' => 'Three'],
]);

$result = $collection->map(function($a) {
    return $a['id'] + 1;
});

assert($result === [2, 3, 4]);

