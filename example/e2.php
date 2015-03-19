<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Functional as f;
use Monad\Either;
use Monad\Utils;

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
assert($concat->orElse(Utils::returns) === 'File "aaa" does not exists');
